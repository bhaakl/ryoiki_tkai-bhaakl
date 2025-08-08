namespace WebApp.RESTClients;

public interface ICatalogManagementAPI
{   
    [Get("/assets")]
    Task<List<Asset>> GetAssets();

    [Get("/assets/{id}")]
    Task<Asset> GetAssetByAssetId([AliasAs("id")] string assetId);

    [Post("/assets")]
    Task RegisterAsset(RegisterAsset command);


    [Get("/bookings")]
    Task<List<Booking>> GetBookings();

    [Get("/bookings/{id}")]
    Task<Booking> GetBookingByBookingId([AliasAs("id")] string bookingId);

    [Post("/bookings")]
    Task RegisterBooking(RegisterBooking command);
}