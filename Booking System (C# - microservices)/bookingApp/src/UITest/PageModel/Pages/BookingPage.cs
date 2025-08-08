using OpenQA.Selenium;

namespace BookingApp.UITest.PageModel.Pages
{
    /// <summary>
    /// Base class for all the pages.
    /// </summary>
    public class BookingPage
    {
        public string Title { get; }
        public BookingApp Booking { get; }

        public IWebDriver WebDriver => Booking.WebDriver;


        /// <summary>
        /// Initialize a new BookingPage instance.
        /// </summary>
        /// <param name="title">The title on the page. This is the text shown as standard title on the page (not the browser window-title!).</param>
        /// <param name="booking">The WebApp instance used for the test.</param>
        public BookingPage(string title, BookingApp booking)
        {
            Title = title;
            Booking = booking;
        }

        /// <summary>
        /// Indication whether the page with the title of the page is shown.
        /// </summary>
        public bool IsActive()
        {
            var header = WebDriver
                .FindElement(By.Id("PageTitle"));
            return header.Text == Title;
        }

        /// <summary>
        /// Gets the current page with the title of the page being shown.
        /// </summary>
        public BookingPage GetActivePageTitle(out string pageTitle)
        {
            var header = WebDriver
                .FindElement(By.Id("PageTitle"));
            pageTitle = header.Text;
            return this;
        }
    }
}