# Smart Expense Splitter (SplitSmart)

Smart Expense Splitter is a web application designed to help groups, roommates, and travel companions accurately track shared expenses and simplify the process of settling debts. The platform eliminates the need for manual calculations by automatically determining net balances and providing optimized settlement plans.

## Features

Key features for managing group expenses include:

- **User Authentication:** Secure account creation and login functionality for individual users.
- **Group Management:** The ability to create dedicated groups for specific purposes, such as trips, shared household utilities, or events.
- **Member Directory:** A search functionality that allows users to find and add other registered platform users to their groups.
- **Expense Tracking:** Users can log individual expenses with descriptions, specify the total cost, and select exactly which group members the expense should be divided among.
- **Net Balances:** A clear, calculated breakdown of the net balance for each member within a group, indicating total amounts owed or due.
- **Settlement Optimization:** An automated settlement calculator that processes all group debts and generates an optimized "How to Settle Up" summary, minimizing the total number of transactions required to clear all balances.
- **Dashboard Integration:** A daily quote feature integrated via a public API, displayed on the user dashboard upon logging in.

## Technology Stack

The application is built using the following technologies:

- **Frontend:** HTML and CSS for the user interface, utilizing jQuery for asynchronous API requests and page updates. FontAwesome is incorporated for iconography.
- **Backend:** PHP handles the core business logic, user authentication, and RESTful API endpoints.
- **Database:** MySQL is used for the structured storage of user profiles, group associations, and expense records.

## Local Installation

To run the application in a local development environment:

1. Ensure a local server environment, such as XAMPP, is installed on the system.
2. Place the project repository folder into the `htdocs` directory of the local server.
3. Open phpMyAdmin and import the provided `database.sql` file to initialize the required database structure and tables.
4. Update the database connection credentials within the PHP API files if the local MySQL instance utilizes a non-standard port (e.g., 3307) or requires a specific password.
5. Access the application via a web browser (e.g., navigating to `http://localhost/Smart-Expense-Splitter-for-Groups`).
