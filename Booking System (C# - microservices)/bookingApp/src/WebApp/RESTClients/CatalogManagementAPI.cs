namespace WebApp.RESTClients;

public class CatalogManagementAPI : ICatalogManagementAPI
{
    private ICatalogManagementAPI _restClient;

    public CatalogManagementAPI(IConfiguration config, HttpClient httpClient)
    {
        string apiHostAndPort = config.GetSection("APIServiceLocations").GetValue<string>("CatalogManagementAPI");
        httpClient.BaseAddress = new Uri($"http://{apiHostAndPort}/api");
        _restClient = RestService.For<ICatalogManagementAPI>(
            httpClient,
            new RefitSettings
            {
                ContentSerializer = new NewtonsoftJsonContentSerializer()
            });
    }

    // asset
    public async Task<List<Asset>> GetAssets()
    {
        return await _restClient.GetAssets();
    }

    public async Task<Asset> GetAssetByAssetId([AliasAs("id")] string assetId)
    {
        try
        {
            return await _restClient.GetAssetByAssetId(assetId);
        }
        catch (ApiException ex)
        {
            if (ex.StatusCode == HttpStatusCode.NotFound)
            {
                return null;
            }
            else
            {
                throw;
            }
        }
    }

    public async Task RegisterAsset(RegisterAsset command)
    {
        await _restClient.RegisterAsset(command);
    }

    // booking
    public async Task<List<Booking>> GetBookings()
    {
        return await _restClient.GetBookings();
    }

    public async Task<Booking> GetBookingByBookingId([AliasAs("id")] string bookingId)
    {
        try
        {
            return await _restClient.GetBookingByBookingId(bookingId);
        }
        catch (ApiException ex)
        {
            if (ex.StatusCode == HttpStatusCode.NotFound)
            {
                return null;
            }
            else
            {
                throw;
            }
        }
    }

    public async Task RegisterBooking(RegisterBooking command)
    {
        await _restClient.RegisterBooking(command);
    }
}