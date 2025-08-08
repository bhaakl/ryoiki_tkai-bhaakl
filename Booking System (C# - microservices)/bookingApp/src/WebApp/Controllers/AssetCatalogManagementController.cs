namespace BookingApp.WebApp.Controllers;

public class AssetCatalogManagementController : Controller
{
    private ICatalogManagementAPI _catalogManagementAPI;
    private readonly Microsoft.Extensions.Logging.ILogger _logger;
    private ResiliencyHelper _resiliencyHelper;

    public AssetCatalogManagementController(ICatalogManagementAPI catalogManagementAPI, ILogger<AssetCatalogManagementController> logger)
    {
        _catalogManagementAPI = catalogManagementAPI;
        _logger = logger;
        _resiliencyHelper = new ResiliencyHelper(_logger);
    }

    [HttpGet]
    public async Task<IActionResult> Index()
    {
        return await _resiliencyHelper.ExecuteResilient(async () =>
        {
            var model = new AssetCatalogViewModel
            {
                Assets = await _catalogManagementAPI.GetAssets()
            };
            return View(model);
        }, View("Offline", new CatalogManagementOfflineViewModel()));
    }

    [HttpGet]
    public async Task<IActionResult> Details(string id)
    {
        return await _resiliencyHelper.ExecuteResilient(async () =>
        {
            Asset asset = await _catalogManagementAPI.GetAssetByAssetId(id);

            var model = new AssetCatalogDetailsViewModel
            {
                Asset = asset
            };
            return View(model);
        }, View("Offline", new CatalogManagementOfflineViewModel()));
    }

    [HttpGet]
    public async Task<IActionResult> New()
    {
        return await _resiliencyHelper.ExecuteResilient(async () =>
        {
            var model = new AssetCatalogNewViewModel
            {
                Asset = new Asset(),
            };
            return View(model);
        }, View("Offline", new CatalogManagementOfflineViewModel()));
    }
    
    [HttpPost]
    public async Task<IActionResult> Register([FromForm] AssetCatalogNewViewModel inputModel)
    {
        if (ModelState.IsValid)
        {
            return await _resiliencyHelper.ExecuteResilient(async () =>
            {
                RegisterAsset cmd = inputModel.MapToRegisterAsset();
                await _catalogManagementAPI.RegisterAsset(cmd);
                return RedirectToAction("Index");
            }, View("Offline", new CatalogManagementOfflineViewModel()));
        }
        else
        {
            return View("New", inputModel);
        }
    }

    [HttpGet]
    public IActionResult Error()
    {
        return View();
    }
}