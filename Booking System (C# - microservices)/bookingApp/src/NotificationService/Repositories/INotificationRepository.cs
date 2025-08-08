namespace BookingApp.NotificationService.Repositories;

public interface INotificationRepository
{
    Task RegisterCustomerAsync(Customer customer);
    Task<Customer> GetCustomerAsync(string customerId);
    Task RegisterAssetAsync(Asset asset);
    Task<Asset> GetAssetAsync(string assetId);
    Task RegisterBookingAsync(Booking booking);
    Task<Booking> GetBookingAsync(string bookingId);
}