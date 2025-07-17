+-----------------------------+
|     Carrier-Based Rules     |
+-----------------------------+

    ┌───────────────────────────────────────────────┐
    │ FedEx Customer                                │
    │ - Friday = ❌ Not Allowed                     │
    │ - Show: “Not Available”                       │
    │ - Suggest selecting Mon–Thu only ✅           │
    └───────────────────────────────────────────────┘
 
    ┌───────────────────────────────────────────────┐
    │ Delivery (Virgin Farms)                       │
    │ - Tuesday Delivery ✅                         │
    │ - Must order by Monday deadline ⏰            │
    │ - After Monday → ❌ Hide inventory or show:   │
    │   “Too late for this week’s delivery”         │
    └───────────────────────────────────────────────┘
 
    ┌───────────────────────────────────────────────┐
    │ All Others (Pickup, etc.)                     │
    │ - Monday–Friday ✅                            │
    │ - No shipping restrictions                    │
    └───────────────────────────────────────────────┘

FedEx: No Friday shipping (unchanged)
Delivery:
Shows Tuesday delivery only
Allows shopping only if today is Monday
All Others: Full week access

Virgin Farms (ID 17): Only Monday is allowed. Otherwise, it blocks and shows an error.
FedEx (IDs 19, 20, 23): Friday is not allowed, and a clear message is returned.
FedEx Priority Overnight + Pick Up (IDs 23, 32): If ordering today after 3:30 PM with no cart yet, block the order.
All other carriers (e.g., Armellini, Prime, etc.): No special restrictions
This is now logic for now.
