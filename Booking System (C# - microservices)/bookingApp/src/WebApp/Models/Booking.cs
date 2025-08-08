namespace BookingApp.WebApp.Models;

public class Booking
{
    public string BookingId { get; set; }

    // [Required]
    [Display(Name = "Asset")]
    public string AssetId { get; set; }

    // [Required]
    [Display(Name = "Customer")]
    public string CustomerId { get; set; }

    [Required]
    [Display(Name = "Start time")]
    public string StartTime { get; set; }

    [Required]
    [Display(Name = "End time")]
    public string EndTime { get; set; }
}