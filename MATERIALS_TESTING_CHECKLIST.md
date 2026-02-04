# Materials System - Comprehensive Testing Checklist

## System Features Overview
- **Multi-Item Materials**: Support for files, YouTube videos, and URL links
- **Category System**: 3 categories (Madd Rules, Idgham Billa Ghunnah, Idgham Bi Ghunnah)
- **AI Category Suggestion**: Optional AI-powered category recommendation
- **External Search**: Tavily API integration (limited to 4 results)
- **Preview Functionality**: Preview search results before adding
- **Filtering**: Filter materials by category with counts

---

## Test 1: Create Material with AI Suggestion

### Steps:
1. Navigate to `/teacher/Materials/create`
2. Enter title: "Understanding Madd Al-Lazim"
3. Enter description: "Detailed explanation of Madd Al-Lazim rules with examples"
4. Click "AI Suggest" button
5. Verify AI suggests correct category
6. Keep or change the suggested category
7. Do NOT add any items yet
8. Click "Create Material"

### Expected Results:
- ✅ AI suggestion returns a category within 3-5 seconds
- ✅ Category dropdown auto-fills with suggested value
- ✅ Material is created successfully
- ✅ Redirect to materials list
- ✅ New material appears with correct category badge

---

## Test 2: Create Material with Online Search & Preview

### Steps:
1. Navigate to `/teacher/Materials/create`
2. Enter title: "Ghunna Rules Tutorial"
3. Enter description: "Comprehensive guide to Ghunna pronunciation"
4. Select category: "Idgham Bi Ghunnah"
5. In search bar, enter: "Ghunna rules Tajweed"
6. Click "Search Online Materials"
7. Wait for results (should be max 4 results)
8. **Click on a search result card to preview**
9. Verify preview modal opens with:
   - Full title
   - Clickable URL
   - Complete content preview
   - "Add as Item" button
   - "Open in New Tab" button
10. Click "Add as Item" in preview modal
11. Verify the item appears in "Added Materials" section
12. Repeat for another search result
13. Click "Create Material"

### Expected Results:
- ✅ Search returns exactly 4 results (not 10)
- ✅ Each result shows title, snippet, and URL
- ✅ Clicking result opens preview modal (not immediately adds)
- ✅ Preview shows full content
- ✅ "Add as Item" button adds the URL to form
- ✅ Added items appear in materials section
- ✅ Material is created with all items
- ✅ Database has 1 material + 2 material_items

---

## Test 3: Create Material with Mixed Items

### Steps:
1. Navigate to `/teacher/Materials/create`
2. Enter title: "Complete Idgham Guide"
3. Enter description: "All types of Idgham explained"
4. Select category manually: "Idgham Billa Ghunnah"
5. Add Item 1 - File Upload:
   - Type: File
   - Upload a PDF file
   - Title: "Idgham Reference Chart"
   - Description: "Visual guide for Idgham rules"
6. Add Item 2 - YouTube:
   - Type: YouTube
   - Link: `https://www.youtube.com/watch?v=dQw4w9WgXcQ`
   - Title: "Idgham Video Tutorial"
   - Description: "Step by step explanation"
7. Add Item 3 - URL:
   - Type: URL
   - Link: `https://example.com/idgham-article`
   - Title: "Idgham Article"
   - Description: "Written guide"
8. Upload a thumbnail image
9. Click "Create Material"

### Expected Results:
- ✅ All 3 items are added to form successfully
- ✅ Each item shows correct type badge
- ✅ Material is created
- ✅ Thumbnail is uploaded and displayed
- ✅ YouTube thumbnail auto-extracted if no thumbnail uploaded
- ✅ Database: 1 material + 3 material_items
- ✅ Files stored in storage/app/public/materials/

---

## Test 4: Category Filtering

### Steps:
1. Navigate to `/teacher/Materials`
2. Verify the filter buttons are visible:
   - All Materials (with count)
   - Madd Rules (with count)
   - Idgham Billa Ghunnah (with count)
   - Idgham Bi Ghunnah (with count)
3. Click "All Materials"
4. Count displayed materials
5. Click "Madd Rules"
6. Verify only Madd Rules materials are shown
7. Click "Idgham Billa Ghunnah"
8. Verify only Idgham Billa Ghunnah materials are shown
9. Click "Idgham Bi Ghunnah"
10. Verify only Idgham Bi Ghunnah materials are shown

### Expected Results:
- ✅ Filter buttons show correct counts
- ✅ Active filter is highlighted
- ✅ Materials are filtered correctly
- ✅ Category badges match the filter
- ✅ Counts are accurate

---

## Test 5: View Material with All Items

### Steps:
1. Navigate to `/teacher/Materials`
2. Click on the material created in Test 3 (with 3 items)
3. Verify page displays:
   - Material title
   - Material description
   - Category badge
   - Item count (e.g., "3 Items")
4. Expand/view items section
5. Verify each item displays:
   - Item type badge (File/YouTube/URL)
   - Item title
   - Item description
   - Appropriate action button:
     - File: "Download" button
     - YouTube: "Watch on YouTube" button
     - URL: "Open Link" button
6. Click "Download" on file item
7. Click "Watch on YouTube" on YouTube item
8. Click "Open Link" on URL item

### Expected Results:
- ✅ All material details are displayed correctly
- ✅ Category badge shows correct category
- ✅ Item count is accurate
- ✅ All 3 items are listed
- ✅ Each item has correct type badge
- ✅ Download button downloads the file
- ✅ YouTube button opens video in new tab
- ✅ URL button opens link in new tab

---

## Test 6: Required Category Validation

### Steps:
1. Navigate to `/teacher/Materials/create`
2. Enter title: "Test Material"
3. Enter description: "Testing required category"
4. **Do NOT select a category**
5. Try to click "Create Material"

### Expected Results:
- ✅ Form validation prevents submission
- ✅ Browser shows "Please select an item from the list" or similar
- ✅ Category dropdown is highlighted as required
- ✅ Material is NOT created

---

## Test 7: AI Suggestion Failure Handling

### Steps:
1. Navigate to `/teacher/Materials/create`
2. Enter title: "Random Text XYZABC123"
3. Enter description: "Nonsense content that AI cannot categorize"
4. Click "AI Suggest"
5. Wait for response

### Expected Results:
- ✅ AI returns error message or no suggestion
- ✅ Category hint shows "Unable to determine category. Please select manually."
- ✅ Category dropdown remains empty
- ✅ Teacher must manually select category
- ✅ Form can still be submitted with manual selection

---

## Test 8: Search Result Limit (4 Results)

### Steps:
1. Navigate to `/teacher/Materials/create`
2. Enter a very broad search term: "Tajweed"
3. Click "Search Online Materials"
4. Count the number of results displayed

### Expected Results:
- ✅ Exactly 4 results are shown (not more)
- ✅ Search completes successfully
- ✅ Results are relevant to the query

---

## Test 9: Delete Material (Cascade)

### Steps:
1. Navigate to `/teacher/Materials`
2. Select a material with multiple items
3. Note the file paths in storage (if any files were uploaded)
4. Click "Delete" or similar action
5. Confirm deletion
6. Check database:
   ```sql
   SELECT * FROM materials WHERE material_id = X;
   SELECT * FROM material_items WHERE material_id = X;
   ```
7. Check storage folder for files

### Expected Results:
- ✅ Material is deleted from materials table
- ✅ All related items are deleted from material_items table (cascade)
- ✅ Physical files are removed from storage/app/public/materials/
- ✅ Thumbnail is removed from storage/app/public/thumbnails/
- ✅ Material no longer appears in materials list

---

## Test 10: Multiple Items Addition/Removal

### Steps:
1. Navigate to `/teacher/Materials/create`
2. Enter basic information
3. Add 5 items of different types
4. Remove 2 items
5. Add 3 more items
6. Verify the count shows correct number
7. Submit form

### Expected Results:
- ✅ Can add unlimited items
- ✅ Remove button works correctly
- ✅ Item counter updates accurately
- ✅ All remaining items are saved
- ✅ Removed items are NOT saved

---

## Database Verification Commands

After creating materials, verify in database:

```sql
-- Check materials table
SELECT material_id, title, category, created_at FROM materials ORDER BY created_at DESC LIMIT 5;

-- Check material_items table
SELECT mi.item_id, mi.material_id, mi.type, mi.title, m.title as material_title
FROM material_items mi
JOIN materials m ON mi.material_id = m.material_id
ORDER BY mi.item_id DESC LIMIT 10;

-- Check item counts per material
SELECT m.material_id, m.title, COUNT(mi.item_id) as item_count
FROM materials m
LEFT JOIN material_items mi ON m.material_id = mi.material_id
GROUP BY m.material_id, m.title;

-- Check category distribution
SELECT category, COUNT(*) as count
FROM materials
WHERE category IS NOT NULL
GROUP BY category;
```

---

## Manual Testing Summary

| Test | Status | Notes |
|------|--------|-------|
| 1. AI Suggestion | ⬜ | |
| 2. Search & Preview | ⬜ | |
| 3. Mixed Items | ⬜ | |
| 4. Category Filtering | ⬜ | |
| 5. View Material | ⬜ | |
| 6. Required Category | ⬜ | |
| 7. AI Failure | ⬜ | |
| 8. Search Limit | ⬜ | |
| 9. Delete Material | ⬜ | |
| 10. Multiple Items | ⬜ | |

---

## Critical Issues Found

Document any issues here during testing:

1. 
2. 
3. 

---

## Testing Completed By

**Name:** ___________________  
**Date:** ___________________  
**All Tests Passed:** ☐ Yes  ☐ No

**Notes:**
