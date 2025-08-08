namespace BookingApp.WebApp.Controllers;

public class BookingCatalogManagementController : Controller
{
    private ICatalogManagementAPI _catalogManagementAPI;
    private ICustomerManagementAPI _customerManagementAPI;
    private readonly Microsoft.Extensions.Logging.ILogger _logger;
    private ResiliencyHelper _resiliencyHelper;

    public BookingCatalogManagementController(ICatalogManagementAPI catalogManagementAPI, ICustomerManagementAPI customerManagementAPI, ILogger<BookingCatalogManagementController> logger)
    {
        _catalogManagementAPI = catalogManagementAPI;
        _customerManagementAPI = customerManagementAPI;
        _logger = logger;
        _resiliencyHelper = new ResiliencyHelper(_logger);
    }

    [HttpGet]
    public async Task<IActionResult> Index()
    {
        return await _resiliencyHelper.ExecuteResilient(async () =>
        {
            var model = new BookingCatalogViewModel
            {
                Bookings = await _catalogManagementAPI.GetBookings(),
            };
            return View(model);
        }, View("Offline", new CatalogManagementOfflineViewModel()));
    }

    [HttpGet]
    public async Task<IActionResult> Details(string id)
    {
        return await _resiliencyHelper.ExecuteResilient(async () =>
        {
            Booking booking = await _catalogManagementAPI.GetBookingByBookingId(id);
            Asset asset = await _catalogManagementAPI.GetAssetByAssetId(booking.AssetId);
            Customer customer = await _customerManagementAPI.GetCustomerById(booking.CustomerId);

            var model = new BookingCatalogDetailsViewModel
            {
                Booking = booking,
                Asset = asset.Name,
                Owner = customer.Name,
            };
            return View(model);
        }, View("Offline", new CatalogManagementOfflineViewModel()));
    }

    [HttpGet]
    public async Task<IActionResult> New()
    {
        return await _resiliencyHelper.ExecuteResilient(async () =>
        {
            var assets = await _catalogManagementAPI.GetAssets();
            var customers = await _customerManagementAPI.GetCustomers();

            var model = new BookingCatalogNewViewModel
            {
                Booking = new Booking(),
                Assets = assets.Select(a => new SelectListItem { Value = a.AssetId, Text = a.Name }),
                Customers = customers.Select(c => new SelectListItem { Value = c.CustomerId, Text = c.Name })
            };
            return View(model);
        }, View("Offline", new CatalogManagementOfflineViewModel()));
    }
    
    [HttpPost]
    public async Task<IActionResult> Register([FromForm] BookingCatalogNewViewModel inputModel)
    {
        // if (ModelState.IsValid)
        // {
        //     return await _resiliencyHelper.ExecuteResilient(async () =>
        //     {
        //         RegisterBooking cmd = inputModel.MapToRegisterBooking();
        //         await _catalogManagementAPI.RegisterBooking(cmd);
        //         return RedirectToAction("Index");
        //     }, View("Offline", new CatalogManagementOfflineViewModel()));
        // }
        // else
        // {
        //     return View("New", inputModel);
        // }

        _logger.LogInformation("============================== Register action ==============================");
        _logger.LogInformation("ModelState.IsValid = {IsValid}", ModelState.IsValid);
        if (!ModelState.IsValid)
        {
            foreach (var kv in ModelState)
                foreach (var err in kv.Value.Errors)
                    _logger.LogWarning("  {Key}: {Error}", kv.Key, err.ErrorMessage);
            return View("New", inputModel);
        }

        _logger.LogInformation("Model valid, mapping command");
        var cmd = inputModel.MapToRegisterBooking();
        _logger.LogInformation("Mapped RegisterBooking: {@cmd}", cmd);
        
        return await _resiliencyHelper.ExecuteResilient(async () =>
        {
            _logger.LogInformation("About to call RegisterBooking on API");
            await _catalogManagementAPI.RegisterBooking(cmd);
            _logger.LogInformation("API call completed");
            return RedirectToAction("Index");
        },
        View("Offline", new CatalogManagementOfflineViewModel()));
    }

    [HttpGet]
    public IActionResult Error()
    {
        return View();
    }
}