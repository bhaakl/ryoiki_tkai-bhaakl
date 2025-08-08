using OpenQA.Selenium;

namespace BookingApp.UITest.PageModel.Pages
{
    /// <summary>
    /// Represents the Home page.
    /// </summary>
    public class HomePage : BookingPage
    {
        public HomePage(BookingApp booking) : base("BookingApp - Booking Management System", booking)
        {
        }
    }
}