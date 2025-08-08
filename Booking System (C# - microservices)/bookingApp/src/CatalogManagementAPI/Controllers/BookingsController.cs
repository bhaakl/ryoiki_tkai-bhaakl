namespace BookingApp.CatalogManagement.Controllers;

[Route("/api/[controller]")]
public class BookingsController : Controller
{
    IMessagePublisher _messagePublisher;
    BookingManagementDBContext _dbContext;

    public BookingsController(BookingManagementDBContext dbContext, IMessagePublisher messagePublisher)
    {
        _dbContext = dbContext;
        _messagePublisher = messagePublisher;
    }

    [HttpGet]
    public async Task<IActionResult> GetAllAsync()
    {
        return Ok(await _dbContext.Bookings.ToListAsync());
    }

    [HttpGet]
    [Route("{bookingId}", Name = "GetByBookingId")]
    public async Task<IActionResult> GetByBookingId(string bookingId)
    {
        var booking = await _dbContext.Bookings.FirstOrDefaultAsync(v => v.BookingId == bookingId);
        if (booking == null)
        {
            return NotFound();
        }
        return Ok(booking);
    }

    [HttpPost]
    public async Task<IActionResult> RegisterAsync([FromBody] RegisterBooking command)
    {
            // _logger.LogInformation("Received RegisterBooking: {@Command}", command);
            Log.Information("Incoming POST /api/bookings with body: {Body}", command);

        try
        {
            if (ModelState.IsValid)
            {
                // insert booking
                Booking booking = command.MapToBooking();
                _dbContext.Bookings.Add(booking);
                await _dbContext.SaveChangesAsync();

                // send event
                var e = BookingRegistered.FromCommand(command);
                await _messagePublisher.PublishMessageAsync(e.MessageType, e, "");

                //return result
                return CreatedAtRoute("GetByBookingId", new { bookingId = booking.BookingId }, booking);
            }
            return BadRequest();
        }
        catch (DbUpdateException)
        {
            ModelState.AddModelError("", "Unable to save changes. " +
                "Try again, and if the problem persists " +
                "see your system administrator.");
            return StatusCode(StatusCodes.Status500InternalServerError);
        }
    }
}
