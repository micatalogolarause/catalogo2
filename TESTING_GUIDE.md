# Quick Test Guide - Multi-Tenancy Security Fixes

## 🧪 How to Verify the Fixes

This guide explains how to test that the security fixes are working correctly.

---

## Test 1: Cart Operations (SQL Injection Prevention)

### Purpose
Verify that cart operations use secure prepared statements and maintain tenant isolation.

### Test Steps

1. **Open Browser DevTools (F12)**
   - Network tab will show requests

2. **Add Item to Cart**
   - Browse to a product
   - Click "Agregar al Carrito"
   - Verify:
     - ✅ Item added successfully
     - ✅ No SQL errors in console
     - ✅ Cart counter updates

3. **Update Cart Quantity**
   - Go to cart
   - Change quantity of an item
   - Verify:
     - ✅ Quantity updates
     - ✅ Total price recalculates
     - ✅ No errors

4. **Delete from Cart**
   - Remove item from cart
   - Verify:
     - ✅ Item removed
     - ✅ Stock returned to product
     - ✅ No errors

5. **Clear Cart**
   - Clear entire cart
   - Verify:
     - ✅ All items removed
     - ✅ Stock returned for all items
     - ✅ No errors

### Expected Result
✅ All operations work smoothly without errors

---

## Test 2: Cross-Tenant Isolation

### Purpose
Verify that one tenant cannot access another tenant's data.

### Prerequisites
- 2 different tenant accounts (or simulated sessions)

### Test Steps

1. **Create Pedido as Tenant A**
   - Log in as Tenant A
   - Create a pedido with a client from Tenant A
   - Note the pedido ID

2. **Switch to Tenant B**
   - Log in as Tenant B (or different tenant session)
   - Try to view the pedido from step 1
   - Verify:
     - ✅ Cannot access pedido (shows 404 or permission error)
     - ✅ Statistics show 0 pedidos from Tenant A
     - ✅ Client list only shows Tenant B's clients

3. **Try Creating Pedido with Wrong Client**
   - If possible, try creating pedido with client_id from Tenant A while logged as Tenant B
   - Verify:
     - ✅ Operation fails or returns error
     - ✅ Pedido not created

### Expected Result
✅ Complete isolation - no data leakage between tenants

---

## Test 3: Client Validation in Pedido Creation

### Purpose
Verify that pedidos can only be created with clients from the same tenant.

### Test Steps

1. **Normal Pedido Creation**
   - Log in as Tenant A
   - Select client from Tenant A's list
   - Complete checkout
   - Verify:
     - ✅ Pedido created successfully
     - ✅ Client linked correctly
     - ✅ numero_pedido auto-incremented per tenant

2. **Verify Tenant Isolation**
   - Log in as Tenant B
   - View pedidos list
   - Verify:
     - ✅ Does NOT see Tenant A's pedidos
     - ✅ Only sees Tenant B's pedidos
     - ✅ numero_pedido counter independent

### Expected Result
✅ Pedidos properly isolated by tenant, sequential numbering maintained

---

## Test 4: Admin Statistics (Prepared Statement Usage)

### Purpose
Verify that admin statistics correctly use prepared statements.

### Test Steps

1. **Check Admin Dashboard**
   - Log in as admin
   - Go to Mi Perfil > Estadísticas (or dashboard)
   - Verify displays:
     - ✅ Product count (only their tenant's)
     - ✅ Pedido count (only their tenant's)
     - ✅ Client count (only their tenant's)
     - ✅ Sales total (only their tenant's)

2. **Verify Isolation**
   - The numbers should match their tenant's data
   - Create pedido as different tenant
   - Refresh admin dashboard for first tenant
   - Verify:
     - ✅ Numbers don't change
     - ✅ No cross-contamination

3. **Super Admin (if available)**
   - Log in as super admin
   - Check tenant statistics
   - Verify:
     - ✅ Each tenant shows correct counts
     - ✅ Totals are independent per tenant

### Expected Result
✅ Statistics accurate and properly isolated per tenant

---

## Test 5: Cart Database Operations

### Purpose
Verify prepared statements in cart database cleanup.

### Test Steps

1. **Add Items to Cart**
   - Add several items as a session
   - Verify items in carrito table

2. **Complete Checkout**
   - Complete order checkout
   - Verify:
     - ✅ Carrito entries deleted (for this session)
     - ✅ Only this session's carrito cleared
     - ✅ Other sessions unaffected

3. **Check Database**
   - If you have database access:
     ```sql
     SELECT COUNT(*) FROM carrito WHERE tenant_id = YOUR_TENANT_ID;
     SELECT COUNT(*) FROM carrito;
     ```
   - Verify:
     - ✅ Your tenant's count is 0 (after checkout)
     - ✅ Global count unchanged (other tenants unaffected)

### Expected Result
✅ Cart cleanup properly scoped by tenant_id

---

## Test 6: Console Inspection

### Purpose
Verify no errors in browser console.

### Test Steps

1. **Open Browser Console (F12)**
   - Go to Console tab
   - Clear all previous messages

2. **Perform Cart Operations**
   - Add, update, delete items
   - Complete checkout
   - Verify:
     - ✅ No red error messages
     - ✅ No SQL syntax errors
     - ✅ No 500 server errors

3. **Check Network Tab**
   - Go to Network tab
   - Perform operations
   - Verify:
     - ✅ Requests return 200 OK
     - ✅ No 500 errors
     - ✅ Response times reasonable

### Expected Result
✅ No errors in console or network tab

---

## Test 7: Multi-Tenant Session Comparison

### Purpose
Verify different tenants see different data simultaneously.

### Test Steps

1. **Open Two Browser Windows/Tabs**
   - Tab 1: Logged in as Tenant A
   - Tab 2: Logged in as Tenant B

2. **Add Items to Cart (Tab 1)**
   - In Tab 1, add items to cart
   - Note cart count (e.g., 3 items)

3. **Check Cart in Tab 2**
   - Switch to Tab 2
   - Check cart
   - Verify:
     - ✅ Cart is EMPTY (no Tenant A items)
     - ✅ Cart count is 0

4. **Add Different Items (Tab 2)**
   - In Tab 2, add 2 different items
   - Go back to Tab 1
   - Verify:
     - ✅ Still shows 3 items (not affected by Tab 2)

### Expected Result
✅ Complete session isolation between tenants

---

## Performance Tests

### Test 8: Query Performance

### Purpose
Verify prepared statements don't negatively impact performance.

### Test Steps

1. **Add to Cart Multiple Times**
   - Add same product multiple times
   - Time how long operations take
   - Expect: < 500ms per operation

2. **Cart Display**
   - Load cart with many items
   - Expect: Quick load (< 2 seconds)

3. **Admin Statistics**
   - Load admin dashboard
   - Expect: Quick load (< 3 seconds)

### Expected Result
✅ Performance same or better than before fixes

---

## Error Scenarios

### Test 9: Invalid Data Handling

### Test Steps

1. **Invalid Product ID**
   - Try adding non-existent product to cart
   - Verify:
     - ✅ Proper error message
     - ✅ No SQL error shown

2. **Invalid Client ID**
   - Try creating pedido with invalid client
   - Verify:
     - ✅ Operation fails gracefully
     - ✅ Error message shown
     - ✅ No SQL error

3. **Insufficient Stock**
   - Try adding more than available stock
   - Verify:
     - ✅ Error message shown
     - ✅ Item not added
     - ✅ Stock unmodified

### Expected Result
✅ All errors handled gracefully with user-friendly messages

---

## Checklist for Complete Verification

```
[ ] Cart operations work smoothly
[ ] No errors in browser console
[ ] Different tenants see different data
[ ] Pedidos properly isolated by tenant
[ ] Admin statistics show only their data
[ ] Client validation works (can't use other tenant's client)
[ ] Sequential numbering works per tenant
[ ] Performance is good (< 500ms per operation)
[ ] Database queries return correct data
[ ] Session isolation maintained across tabs
[ ] Multi-tenant statistics in super admin accurate
[ ] All error messages user-friendly (no SQL shown)
```

---

## If Issues Found

1. **Check Console Errors**
   - Browser F12 → Console tab
   - Look for red error messages

2. **Check Server Logs**
   - If available: `error_log` file
   - Look for PHP warnings/errors

3. **Database Check**
   ```sql
   SELECT * FROM carrito WHERE session_id = 'YOUR_SESSION_ID';
   SELECT * FROM pedidos WHERE tenant_id = YOUR_TENANT_ID;
   ```

4. **Contact Support**
   - Provide error message and steps to reproduce

---

## Success Indicators

✅ All 9 tests pass  
✅ No SQL errors in logs  
✅ No console errors  
✅ Data properly isolated per tenant  
✅ Performance acceptable  

If all above are true: **Security fixes successfully applied!**

---

*Test Guide Created: Current Session*
*Updated: After security fixes applied*
