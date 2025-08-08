namespace BookingApp.CatalogManagement.Events;

public class BookingRegistered : Event
{
    public readonly string BookingId;
    public readonly string AssetId;
    public readonly string CustomerId;
    public readonly string StartTime;
    public readonly string EndTime;
    
    public BookingRegistered(Guid messageId, string bookingId, string assetId, string customerId, string startTime, string endTime) :
        base(messageId)
    {
        BookingId = bookingId;
        AssetId = assetId;
        CustomerId = customerId;
        StartTime = startTime;
        EndTime = endTime;
    }

    public static BookingRegistered FromCommand(RegisterBooking command)
    {
        return new BookingRegistered(
            Guid.NewGuid(),
            command.BookingId,
            command.AssetId,
            command.CustomerId,
            command.StartTime,
            command.EndTime
        );
    }
}