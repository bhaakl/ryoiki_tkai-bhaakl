namespace BookingApp.CatalogManagement.Model;

public class Booking
{
    public string BookingId { get; set; }
    public string AssetId { get; set; }
    public string CustomerId { get; set; }
    public string StartTime { get; set; }
    public string EndTime { get; set; }
}