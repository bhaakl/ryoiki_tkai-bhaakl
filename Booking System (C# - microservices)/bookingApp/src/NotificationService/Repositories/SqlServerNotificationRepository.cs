namespace BookingApp.NotificationService.Repositories;

public class SqlServerNotificationRepository : INotificationRepository
{
    private string _connectionString;

    public SqlServerNotificationRepository(string connectionString)
    {
        _connectionString = connectionString;

        // init db
        Log.Information("Initialize Database");

        Policy
        .Handle<Exception>()
        .WaitAndRetryAsync(10, r => TimeSpan.FromSeconds(10), (ex, ts) => { Log.Error("Error connecting to DB. Retrying in 10 sec."); })
        .ExecuteAsync(InitializeDBAsync)
        .Wait();
    }

    public async Task RegisterCustomerAsync(Customer customer)
    {
        using (SqlConnection conn = new SqlConnection(_connectionString))
        {
            string sql =
                "insert into Customer(CustomerId, Name, TelephoneNumber, EmailAddress) " +
                "values(@CustomerId, @Name, @TelephoneNumber, @EmailAddress);";
            await conn.ExecuteAsync(sql, customer);
        }
    }

    public async Task<Customer> GetCustomerAsync(string customerId)
    {
        using (SqlConnection conn = new SqlConnection(_connectionString))
        {
            return await conn.QueryFirstOrDefaultAsync<Customer>("select * from Customer where CustomerId = @CustomerId",
                new { CustomerId = customerId });
        }
    }

    public async Task RegisterAssetAsync(Asset asset)
    {
        using (SqlConnection conn = new SqlConnection(_connectionString))
        {
            string sql =
                "insert into Asset(AssetId, Name, Type, Description) " +
                "values(@AssetId, @Name, @Type, @Description);";
            await conn.ExecuteAsync(sql, asset);
        }
    }

    public async Task<Asset> GetAssetAsync(string assetId)
    {
        using (SqlConnection conn = new SqlConnection(_connectionString))
        {
            return await conn.QueryFirstOrDefaultAsync<Asset>("select * from Asset where AssetId = @AssetId",
                new { AssetId = assetId });
        }
    }

    public async Task RegisterBookingAsync(Booking booking)
    {
        using (SqlConnection conn = new SqlConnection(_connectionString))
        {
            string sql =
                "insert into Booking(BookingId, AssetId, CustomerId, StartTime, EndTime) " +
                "values(@BookingId, @AssetId, @CustomerId, @StartTime, @EndTime);";
            await conn.ExecuteAsync(sql, booking);
        }
    }

    public async Task<Booking> GetBookingAsync(string bookingId)
    {
        using (SqlConnection conn = new SqlConnection(_connectionString))
        {
            return await conn.QueryFirstOrDefaultAsync<Booking>("select * from Booking where BookingId = @BookingId",
                new { BookingId = bookingId });
        }
    }

    private async Task InitializeDBAsync()
    {
        using (SqlConnection conn = new SqlConnection(_connectionString.Replace("Notification", "master")))
        {
            await conn.OpenAsync();

            // create database
            string sql =
                "IF NOT EXISTS(SELECT * FROM master.sys.databases WHERE name='Notification') CREATE DATABASE Notification;";

            await conn.ExecuteAsync(sql);
        }

        // create tables
        using (SqlConnection conn = new SqlConnection(_connectionString))
        {
            await conn.OpenAsync();

            // create tables
            string sql = "IF OBJECT_ID('Customer') IS NULL " +
                  "CREATE TABLE Customer (" +
                  "  CustomerId varchar(50) NOT NULL," +
                  "  Name varchar(50) NOT NULL," +
                  "  TelephoneNumber varchar(50)," +
                  "  EmailAddress varchar(50)," +
                  "  PRIMARY KEY(CustomerId));" +

                  "IF OBJECT_ID('Asset') IS NULL " +
                  "CREATE TABLE Asset (" +
                  "  AssetId varchar(50) NOT NULL," +
                  "  Name varchar(50) NOT NULL," +
                  "  Type varchar(50) NOT NULL," +
                  "  Description varchar(250)," +
                  "  PRIMARY KEY(AssetId));" +
                  
                  "IF OBJECT_ID('Booking') IS NULL " +
                  "CREATE TABLE Booking (" +
                  "  BookingId varchar(50) NOT NULL," +
                  "  AssetId varchar(50) NOT NULL," +
                  "  CustomerId varchar(50) NOT NULL," +
                  "  StartTime varchar(250)," +
                  "  EndTime varchar(250)," +
                  "  PRIMARY KEY(BookingId));";

            await conn.ExecuteAsync(sql);
        }
    }
}