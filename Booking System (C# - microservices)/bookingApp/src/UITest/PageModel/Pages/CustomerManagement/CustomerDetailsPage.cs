using OpenQA.Selenium;

namespace BookingApp.UITest.PageModel.Pages.CustomerManagement
{
    /// <summary>
    /// Represents the CustomerDetails page.
    /// </summary>
    public class CustomerDetailsPage : BookingPage
    {
        public CustomerDetailsPage(BookingApp booking) : base("Customer Management - details", booking)
        {
        }

        public CustomerManagementPage Back()
        {
            WebDriver.FindElement(By.Id("BackButton")).Click();
            return new CustomerManagementPage(Booking);
        }
    }
}