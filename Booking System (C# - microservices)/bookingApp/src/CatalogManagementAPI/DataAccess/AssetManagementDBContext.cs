namespace BookingApp.CatalogManagement.DataAccess;

public class AssetManagementDBContext : DbContext
{
    public AssetManagementDBContext(DbContextOptions<AssetManagementDBContext> options) : base(options)
    {
    }

    public DbSet<Asset> Assets { get; set; }

    protected override void OnModelCreating(ModelBuilder builder)
    {
        builder.Entity<Asset>().HasKey(m => m.AssetId);
        builder.Entity<Asset>().ToTable("Asset");
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