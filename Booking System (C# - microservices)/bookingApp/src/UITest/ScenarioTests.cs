namespace BookingApp.UITest;

public class ScenarioTests
{
    private readonly ITestOutputHelper _output;

    public ScenarioTests(ITestOutputHelper output)
    {
        _output = output;
    }

    [Fact]
    public void End_To_End()
    {
        // arrange
        string testrunId = Guid.NewGuid().ToString("N");
        BookingApp booking = new BookingApp(testrunId, TestConstants.BookingStartUrl);
        var homePage = booking.Start();
        string licenseNumber = TestDataGenerators.GenerateRandomLicenseNumber();

        // act ...
    }
}