const express = require('express');
const bodyParser = require('body-parser');
const fs = require('fs');
const path = require('path');
const app = express();
const port = 3000;

// Middleware
app.use(express.static('public')); // Serve static files from 'public' directory
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// Ensure data directory exists
const dataDir = path.join(__dirname, 'data');
if (!fs.existsSync(dataDir)) {
    fs.mkdirSync(dataDir);
}

// File to store contact submissions
const contactsFile = path.join(dataDir, 'contacts.json');

// Initialize contacts file if it doesn't exist
if (!fs.existsSync(contactsFile)) {
    fs.writeFileSync(contactsFile, JSON.stringify([]));
}

// Read contacts from file
function getContacts() {
    const data = fs.readFileSync(contactsFile, 'utf8');
    return JSON.parse(data);
}

// Save contacts to file
function saveContacts(contacts) {
    fs.writeFileSync(contactsFile, JSON.stringify(contacts, null, 2));
}

// API endpoint to submit contact form
app.post('/api/submit-contact', (req, res) => {
    try {
        const contacts = getContacts();
        
        // Add unique ID and current date to the contact
        const newContact = {
            id: Date.now().toString(), // Simple ID using timestamp
            ...req.body,
            date: new Date().toISOString(),
            status: 'new'
        };
        
        // Add to contacts array
        contacts.push(newContact);
        
        // Save updated contacts
        saveContacts(contacts);
        
        res.status(200).json({ success: true, message: 'Contact form submitted successfully' });
    } catch (error) {
        console.error('Error saving contact:', error);
        res.status(500).json({ success: false, message: 'Failed to save contact information' });
    }
});

// API endpoint to get all contacts for admin panel
app.get('/api/contacts', (req, res) => {
    try {
        const contacts = getContacts();
        res.json(contacts);
    } catch (error) {
        console.error('Error retrieving contacts:', error);
        res.status(500).json({ success: false, message: 'Failed to retrieve contacts' });
    }
});

// API endpoint to update a contact (for status changes)
app.put('/api/contacts/:id', (req, res) => {
    try {
        const contacts = getContacts();
        const contactId = req.params.id;
        
        // Find the contact by ID
        const contactIndex = contacts.findIndex(contact => contact.id === contactId);
        
        if (contactIndex === -1) {
            return res.status(404).json({ success: false, message: 'Contact not found' });
        }
        
        // Update the contact with new data
        contacts[contactIndex] = {
            ...contacts[contactIndex],
            ...req.body
        };
        
        // Save updated contacts
        saveContacts(contacts);
        
        res.json({ success: true, message: 'Contact updated successfully', contact: contacts[contactIndex] });
    } catch (error) {
        console.error('Error updating contact:', error);
        res.status(500).json({ success: false, message: 'Failed to update contact' });
    }
});

// Serve admin panel
app.get('/admin', (req, res) => {
    res.sendFile(path.join(__dirname, 'public', 'admin.html'));
});

// Start server
app.listen(port, () => {
    console.log(`Server running at http://localhost:${port}`);
});