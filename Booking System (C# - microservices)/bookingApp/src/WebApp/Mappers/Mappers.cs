namespace BookingApp.WebApp.Mappers;

public static class Mappers
{
    public static RegisterCustomer MapToRegisterCustomer(this CustomerManagementNewViewModel source) => new RegisterCustomer
    (
        Guid.NewGuid(),
        Guid.NewGuid().ToString("N"),
        source.Customer.Name,
        source.Customer.Address,
        source.Customer.PostalCode,
        source.Customer.City,
        source.Customer.TelephoneNumber,
        source.Customer.EmailAddress
    );

    public static RegisterAsset MapToRegisterAsset(this AssetCatalogNewViewModel source) => new RegisterAsset(
        Guid.NewGuid(),
        Guid.NewGuid().ToString("N"),
        source.Asset.Name,
        source.Asset.Type,
        source.Asset.Description
    );

    public static RegisterAsset MapToRegisterAsset(this Asset source) => new RegisterAsset(
        Guid.NewGuid(),
        source.AssetId,
        source.Name,
        source.Type,
        source.Description
    );

    public static RegisterBooking MapToRegisterBooking(this BookingCatalogNewViewModel source) => new RegisterBooking(
        Guid.NewGuid(),
        Guid.NewGuid().ToString("N"),
        source.SelectedAssetId,
        source.SelectedCustomerId,
        source.Booking.StartTime,
        source.Booking.EndTime
    );

    public static RegisterBooking MapToRegisterBooking(this Booking source) => new RegisterBooking(
        Guid.NewGuid(),
        source.BookingId,
        source.AssetId,
        source.CustomerId,
        source.StartTime,
        source.EndTime
    );
}