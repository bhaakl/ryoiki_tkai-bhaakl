namespace BookingApp.CatalogManagement.Commands;

public class RegisterBooking : Command
{
    public readonly string BookingId;
    public readonly string AssetId;
    public readonly string CustomerId;
    public readonly string StartTime;
    public readonly string EndTime;

    public RegisterBooking(Guid messageId, string bookingId, string assetId, string customerId, string startTime, string endTime) :
        base(messageId)
    {
        BookingId = bookingId;
        AssetId = assetId;
        CustomerId = customerId;
        StartTime = startTime;
        EndTime = endTime;
    }
}