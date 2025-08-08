namespace BookingApp.WebApp.Commands;

public class RegisterAsset : Command
{
    public readonly string AssetId;
    public readonly string Name;
    public readonly string Type;
    public readonly string Description;

    public RegisterAsset(Guid messageId, string assetId, string name, string type, string description) :
        base(messageId)
    {
        AssetId = assetId;
        Name = name;
        Type = type;
        Description = description;
    }
}