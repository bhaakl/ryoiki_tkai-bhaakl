using OpenQA.Selenium;

namespace BookingApp.UITest.PageModel.Pages
{
    /// <summary>
    /// Represents the About page.
    /// </summary>
    public class AboutPage : BookingPage
    {
        public AboutPage(BookingApp booking) : base("About Booking", booking)
        {
        }
    }
}