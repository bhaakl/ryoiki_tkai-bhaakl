namespace BookingApp.CatalogManagementAPI.Mappers;

public static class Mappers
{
    public static Asset MapToAsset(this RegisterAsset command) => new Asset
    {
        AssetId = command.AssetId,
        Name = command.Name,
        Type = command.Type,
        Description = command.Description
    };

    public static Booking MapToBooking(this RegisterBooking command) => new Booking
    {
        BookingId = command.BookingId,
        AssetId = command.AssetId,
        CustomerId = command.CustomerId,
        StartTime = command.StartTime,
        EndTime = command.EndTime
    };
}