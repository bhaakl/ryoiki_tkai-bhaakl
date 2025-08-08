namespace BookingApp.CatalogManagement.Controllers;

[Route("/api/[controller]")]
public class AssetsController : Controller
{
    IMessagePublisher _messagePublisher;
    AssetManagementDBContext _dbContext;

    public AssetsController(AssetManagementDBContext dbContext, IMessagePublisher messagePublisher)
    {
        _dbContext = dbContext;
        _messagePublisher = messagePublisher;
    }

    [HttpGet]
    public async Task<IActionResult> GetAllAsync()
    {
        return Ok(await _dbContext.Assets.ToListAsync());
    }

    [HttpGet]
    [Route("{assetId}", Name = "GetByAssetId")]
    public async Task<IActionResult> GetByAssetId(string assetId)
    {
        var asset = await _dbContext.Assets.FirstOrDefaultAsync(v => v.AssetId == assetId);
        if (asset == null)
        {
            return NotFound();
        }
        return Ok(asset);
    }

    [HttpPost]
    public async Task<IActionResult> RegisterAsync([FromBody] RegisterAsset command)
    {
        try
        {
            if (ModelState.IsValid)
            {
                // insert asset
                Asset asset = command.MapToAsset();
                _dbContext.Assets.Add(asset);
                await _dbContext.SaveChangesAsync();

                // send event
                var e = AssetRegistered.FromCommand(command);
                await _messagePublisher.PublishMessageAsync(e.MessageType, e, "");

                //return result
                return CreatedAtRoute("GetByAssetId", new { assetId = asset.AssetId }, asset);
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
