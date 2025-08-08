using OpenQA.Selenium;

namespace BookingApp.UITest.PageModel.Pages.CustomerManagement
{
    /// <summary>
    /// Represents the CustomerManagement page.
    /// </summary>
    public class CustomerManagementPage : BookingPage
    {
        public CustomerManagementPage(BookingApp booking) : base("Customer Management - overview", booking)
        {
        }

        public RegisterCustomerPage RegisterCustomer()
        {
            WebDriver.FindElement(By.Id("RegisterCustomerButton")).Click();
            return new RegisterCustomerPage(Booking);
        }

        public CustomerDetailsPage SelectCustomer(string customerName)
        {
            WebDriver
                .FindElement(By.XPath($"//td[contains(text(),'{customerName}')]"))
                .Click();
            return new CustomerDetailsPage(Booking); 
        }
    }
}