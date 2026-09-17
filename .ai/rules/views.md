---
paths:
  - 'resources/views/**'
---

# Views & Guest Access Rules

## Public Welcome Page AI Chatbot Guest Message Cap
- Unauthenticated guests can access and interact with the AI health assistant without logging in.
- Guest sessions are capped at 15 messages (tracked via `localStorage.getItem('usm_guest_msg_count')`).
- An active counter displays remaining messages (`XX/15 messages left`).
- Upon reaching the 15-message cap, the chat input and send button are disabled, and a prominent prompt directs the user to log in or register for unlimited member access.
- Authenticated users receive unlimited messages and an "Unlimited Member Access" badge.

## Pharmacy Storefront Guest Preview & Authentication Gate
- Unauthenticated guests can view the store (`/medicines`) in a limited preview mode (curated sample of products).
- A top notification banner warns guests that they are in preview mode and prompts them to log in for full access to all 35 products and real-time batch stock.
- The product catalog for guests displays a "Full Pharmacy Catalog Locked" card highlighting locked items and requiring login.
- Product cards for guests display a "Log in to Inquire" button. Clicking this button opens a modal explaining that submitting inquiries and reserving stock requires logging in with a verified USM account.
- Authenticated patients and staff have unrestricted access to the full catalog, batch numbers, and active inquiry submission.
