namespace BookingApp.CatalogManagement.Events;

public class AssetRegistered : Event
{
    public readonly string AssetId;
    public readonly string Name;
    public readonly string Type;
    public readonly string Description;
    
    public AssetRegistered(Guid messageId, string assetId, string name, string type, string description) :
        base(messageId)
    {
        AssetId = assetId;
        Name = name;
        Type = type;
        Description = description;
    }

    public static AssetRegistered FromCommand(RegisterAsset command)
    {
        return new AssetRegistered(
            Guid.NewGuid(),
            command.AssetId,
            command.Name,
            command.Type,
            command.Description
        );
    }
}