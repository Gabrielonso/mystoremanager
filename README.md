# mystoremanager
A responsive web application for managing and keeping track record of business activities and resources regardless of location with any device.

mystoremanager was developed using HTML, CSS, JavaScript, Bootstrap, PHP, JQuery and MySql, to manage and handle certain operations of a retail-wholesaling business, where there are several employees and where inventory and trading of goods are done in two categories:
- Warehouse(or wholesale goods) section where goods are managed in cartons or packs as the case may be.
- Retail store (or retail goods) section where goods are managed in pieces.

Certain factors such as speed, flexibility and ease of use where considered in building certain features of the application.

mystoremanager manages the following:

**Users**
- There are three user types for this application: master admin user(only one), admin user and regular user.
- Privileges to create, read, update and delete certain files or record are based on user type. 
- New users can only be created through an admin's account, but only the master admin can grant admin privileges to users(or make regular users admin).
- All users have access to modify/update thier details in the profile section of thier own individual account when signed in.
- Admins can deactivate and activate users account. But only the master admin can do that for all types of users.

**Password recovery**

There are two ways to recover a lost or forgotten password:
- Through an admin user's account.
- Account verification by providing user email and certain details linked with their acount. A one-time-password will be sent to the registered email for password reset.

**Dashbord**
- A section to easily view a summary report of certain transactions and activities.

**Goods Item**
- New goods item can be created, edited or deleted.
- Supplies can be added as a new goods item is being created.
- Prices and the number of unit per carton of a particular goods item can be set too or changed at anytime.

**Stocking and inventory handling**

mystoremanager handles inventory in two categories: the warehouse/wholesale goods section and the retail section.

*Warehouse/wholesale goods section*
- Incoming goods item (purchases/supplies recieved) in cartons are added to the warehouse stock.
- Outgoing goods item (goods added/moved to the retail section or sales made to customers in cartons) are subtracted from the warehouse/wholesale goods section. 
- mystoremanager keeps a record of the total inventory(ins and out) of each goods item made in the warehouse on daily basis. This will automatically reset for all goods at the end of the day.
- It also keeps a track record of the total inventory(starting stock, recieved, shipped and current stock) of each goods item in the warehouse from a set datetime.
This can be reset at anytime for an item, probably when a physical count is made. Then it starts to take stock from that time onwards.

*Retail goods section*
- Goods item in this category are handled in pieces or units of a carton.
- Goods can only be added and are subtracted from the warehouse stocks(in cartons) to this section.
- mystoremanager keeps record of the total inventory (starting stock for the day, ins, outs and current stock) for each item in the retail section only on daily basis. This will automatically reset for all at the end of the day.
The record of any particular item can also be reset at any time and stocks will be taken from that time of the day onwards.

**Purchases**
- All purchases/recieved supplies details and records are managed by mystoremanager.
- All purchases made are added to the warehouse.

**Sales**
- All sales, sales details and records are managed by mystoremanager.
- Sales invoice can be created, printed and edited.
- Sales of an item can be made in both cartons and/or in pieces for a customer. Stocks will be subtracted based on category.
- An item can be easily searched for by an autocomplete search algorithm when a user starts typing out keywords associated with the required item name.
  A list of item containing the letters you typed in will appear for selection.
  If required item does not appear in the list, it probably does not exist in the record or users can try a different keyword or create new item for record that does not exist.
- Clicking on an item from the list of options provided will include that item to the customer's orders(sales invoice).
- Prices are automatically included when item is selected(clicked on).
- Calculations are automatically handled by mystoremanager as more items are added to the sales order/invoice.
- All sales/customers orders can be viewed.

**Payments**
- All payments detail and record are managed by mystoremanager.
