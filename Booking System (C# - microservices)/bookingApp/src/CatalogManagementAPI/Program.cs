var builder = WebApplication.CreateBuilder(args);

// setup logging
builder.Host.UseSerilog((context, logContext) => 
    logContext
        .ReadFrom.Configuration(builder.Configuration)
        .Enrich.WithMachineName()
);

// add DBContext
var sqlConnectionString = builder.Configuration.GetConnectionString("CatalogManagementCN");
builder.Services.AddDbContext<AssetManagementDBContext>(options => options.UseSqlServer(sqlConnectionString));
builder.Services.AddDbContext<BookingManagementDBContext>(options => options.UseSqlServer(sqlConnectionString));

// add messagepublisher
builder.Services.UseRabbitMQMessagePublisher(builder.Configuration);

// Add framework services
builder.Services
    .AddMvc(options => options.EnableEndpointRouting = false)
    .AddNewtonsoftJson();

// Register the Swagger generator, defining one or more Swagger documents
builder.Services.AddSwaggerGen(c =>
{
    c.SwaggerDoc("v1", new OpenApiInfo { Title = "CatalogManagement API", Version = "v1" });
});

// Add health checks
builder.Services.AddHealthChecks()
    .AddDbContextCheck<AssetManagementDBContext>();
builder.Services.AddHealthChecks()
    .AddDbContextCheck<BookingManagementDBContext>();

// Setup MVC
builder.Services.AddControllers();

var app = builder.Build();

if (app.Environment.IsDevelopment())
{
    app.UseDeveloperExceptionPage();
}

// app.UseSerilogRequestLogging();
// app.Use(async (context, next) =>
// {
//     if (context.Request.Path.StartsWithSegments("/api/bookings") 
//         && context.Request.Method == HttpMethods.Post)
//     {
//         // включаем возможность многократного чтения тела
//         context.Request.EnableBuffering();

//         // читаем полностью
//         using var reader = new StreamReader(
//             context.Request.Body,
//             encoding: System.Text.Encoding.UTF8,
//             detectEncodingFromByteOrderMarks: false,
//             leaveOpen: true);

//         var body = await reader.ReadToEndAsync();
//         // возвращаем позицию потока в начало, чтобы MVC мог пересчитать модель
//         context.Request.Body.Position = 0;

//         Log.Information("Incoming POST {Path} with body: {Body}", 
//             context.Request.Path, body);
//     }

//     await next();
// });

app.UseMvc();
app.UseDefaultFiles();
app.UseStaticFiles();

// Enable middleware to serve generated Swagger as a JSON endpoint.
app.UseSwagger();

// Enable middleware to serve swagger-ui (HTML, JS, CSS etc.), specifying the Swagger JSON endpoint.
app.UseSwaggerUI(c =>
{
    c.SwaggerEndpoint("/swagger/v1/swagger.json", "Booking App API - v1");
});

// auto migrate db
using (var scope = app.Services.GetRequiredService<IServiceScopeFactory>().CreateScope())
{
    scope.ServiceProvider.GetService<AssetManagementDBContext>().MigrateDB();
    scope.ServiceProvider.GetService<BookingManagementDBContext>().MigrateDB();
}

app.UseHealthChecks("/hc");

app.MapControllers();

app.Run();
