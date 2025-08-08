namespace BookingApp.WebApp.ViewModels;

public class BookingCatalogNewViewModel
{
    public Booking Booking { get; set; }
    public IEnumerable<SelectListItem> Assets { get; set; }

    [Required(ErrorMessage = "Asset is required")]
    public string SelectedAssetId { get; set; }

    public IEnumerable<SelectListItem> Customers { get; set; }

    [Required(ErrorMessage = "Owner is required")]
    public string SelectedCustomerId { get; set; }
}