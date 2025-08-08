namespace BookingApp.NotificationService;

public class NotificationWorker : IHostedService, IMessageHandlerCallback
{
    IMessageHandler _messageHandler;
    INotificationRepository _repo;
    IEmailNotifier _emailNotifier;

    public NotificationWorker(IMessageHandler messageHandler, INotificationRepository repo, IEmailNotifier emailNotifier)
    {
        _messageHandler = messageHandler;
        _repo = repo;
        _emailNotifier = emailNotifier;
    }

    public Task StartAsync(CancellationToken cancellationToken)
    {
        _messageHandler.Start(this);
        return Task.CompletedTask;
    }

    public Task StopAsync(CancellationToken cancellationToken)
    {
        _messageHandler.Stop();
        return Task.CompletedTask;
    }

    public async Task<bool> HandleMessageAsync(string messageType, string message)
    {
        try
        {
            JObject messageObject = MessageSerializer.Deserialize(message);
            switch (messageType)
            {
                case "CustomerRegistered":
                    await HandleAsync(messageObject.ToObject<CustomerRegistered>());
                    break;
                case "AssetRegistered":
                    await HandleAsync(messageObject.ToObject<AssetRegistered>());
                    break;
                case "BookingRegistered":
                    await HandleAsync(messageObject.ToObject<BookingRegistered>());
                    break;
                default:
                    break;
            }
        }
        catch (Exception ex)
        {
            Log.Error(ex, $"Error while handling {messageType} event.");
        }

        return true;
    }

    private async Task HandleAsync(CustomerRegistered cr)
    {
        Customer customer = new Customer
        {
            CustomerId = cr.CustomerId,
            Name = cr.Name,
            TelephoneNumber = cr.TelephoneNumber,
            EmailAddress = cr.EmailAddress
        };

        Log.Information("Register customer: {Id}, {Name}, {TelephoneNumber}, {Email}",
            customer.CustomerId, customer.Name, customer.TelephoneNumber, customer.EmailAddress);

        await _repo.RegisterCustomerAsync(customer);
    }

    private async Task HandleAsync(AssetRegistered ast)
    {
        Asset asset = new Asset
        {
            AssetId = ast.AssetId,
            Name = ast.Name,
            Type = ast.Type,
            Description = ast.Description
        };

        Log.Information("Register asset: {Id}, {Name}, {Type}, {Description}",
            asset.AssetId, asset.Name, asset.Type, asset.Description);

        await _repo.RegisterAssetAsync(asset);
    }

    private async Task HandleAsync(BookingRegistered bk)
    {
        Booking booking = new Booking
        {
            BookingId = bk.BookingId,
            AssetId = bk.AssetId,
            CustomerId = bk.CustomerId,
            StartTime = bk.StartTime,
            EndTime = bk.EndTime,
        };

        Log.Information("Register booking: {Id}, {AssetId}, {CustomerId}, {StartTime}, {EndTime}",
            booking.BookingId, booking.AssetId, booking.CustomerId, booking.StartTime, booking.EndTime);

        await _repo.RegisterBookingAsync(booking);
    }
}