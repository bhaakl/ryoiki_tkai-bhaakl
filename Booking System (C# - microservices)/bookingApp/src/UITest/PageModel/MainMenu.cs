using OpenQA.Selenium;
namespace BookingApp.UITest.PageModel
{
    public class MainMenu
    {
        private readonly BookingApp _booking;

        public MainMenu(BookingApp booking)
        {
            _booking = booking;
        }

        public HomePage Home()
        {
            _booking.WebDriver.FindElement(By.Id("MainMenu.Home")).Click();
            return new HomePage(_booking);
        }

        public CustomerManagementPage CustomerManagement()
        {
            _booking.WebDriver.FindElement(By.Id("MainMenu.CustomerManagement")).Click();
            return new CustomerManagementPage(_booking);
        }
    }
}