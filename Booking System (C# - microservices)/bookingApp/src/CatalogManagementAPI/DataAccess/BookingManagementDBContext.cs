namespace BookingApp.CatalogManagement.DataAccess;

public class BookingManagementDBContext : DbContext
{
    public BookingManagementDBContext(DbContextOptions<BookingManagementDBContext> options) : base(options)
    {
    }

    public DbSet<Booking> Bookings { get; set; }
    
    protected override void OnModelCreating(ModelBuilder builder)
    {
        builder.Entity<Booking>().HasKey(m => m.BookingId);
        builder.Entity<Booking>().ToTable("Booking");
        base.OnModelCreating(builder);
    }

    public void MigrateDB()
    {
        Policy
            .Handle<Exception>()
            .WaitAndRetry(10, r => TimeSpan.FromSeconds(10))
            .Execute(() => Database.Migrate());
    }
}