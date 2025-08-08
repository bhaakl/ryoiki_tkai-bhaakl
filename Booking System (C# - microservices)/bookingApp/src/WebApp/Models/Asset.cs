namespace BookingApp.WebApp.Models;

public class Asset
{
    public string AssetId { get; set; }

    [Required]
    [Display(Name = "Name")]
    public string Name { get; set; }

    [Required]
    [Display(Name = "Type")]
    public string Type { get; set; }

    [Display(Name = "Description")]
    public string Description { get; set; }
}