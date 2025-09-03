# Project Overview: EOLF Sains

This project is a comprehensive web application built with the Laravel 11 framework. It functions as an Enterprise Resource Planning (ERP) system tailored for managing inventory, sales, customer relations, and logistics for a business that distributes physical goods.

The application is designed for a company with multiple branches and provides a centralized platform to manage the entire lifecycle of its products, from initial stock-in to final delivery and returns.

## Core Features

The system is composed of several interconnected modules:

*   **Inventory Management:** Tracks product stock levels across different branches. It includes features for stock reconciliation, bulk updates, an item master data list, and management of materials inventory.

*   **Order Management:** Handles the creation, editing, and tracking of customer orders (referred to as "Inbound" orders). It supports various order types, including standard paid orders, free-of-charge (FOC) orders, and orders with special fees.

*   **Customer & Store Management:** Maintains a detailed database of customers and their associated store locations. This includes managing contact information, addresses, and store-specific data.

*   **Product Catalog:** Manages a catalog of products, which are categorized by types and variants. It also handles complex pricing rules through different pricing levels.

*   **Sales & Delivery Logistics:** Streamlines the delivery process by generating order slips, loading tickets for warehouse preparation, and final delivery receipts. It also includes management of delivery personnel (drivers) and the vehicle fleet.

*   **Bad Order & Returns Management:** Provides a complete workflow for processing and tracking returned or "bad order" items from customers, ensuring they are correctly accounted for in the inventory system.

*   **Equipment & Asset Tracking:** Manages and tracks company-owned assets (e.g., freezers, equipment) that are deployed at customer stores. This includes tracking assignment, history, and pull-out status.

*   **User & Access Control:** Implements a role-based access control system to manage user permissions, ensuring that employees can only access the features relevant to their roles (e.g., `admin` role for sensitive operations).

*   **Multi-Branch Architecture:** The entire system is designed to support multiple business branches, allowing for data segmentation and management on a per-branch basis.

*   **Reporting:** Features a reporting module to generate critical business insights, such as sales reports, product summaries, and available stock levels.
