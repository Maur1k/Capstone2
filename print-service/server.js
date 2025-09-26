const express = require('express');
const bodyParser = require('body-parser');
const escpos = require('escpos');

// Enable USB adapter
escpos.USB = require('escpos-usb');

let device = null;
let printer = null;
let printerConnected = false;

/**
 * Initialize printer connection
 */
function initializePrinter() {
    try {
        device = new escpos.USB(); // auto-detect first connected printer
        printer = new escpos.Printer(device);
        printerConnected = true;
        console.log('✅ Printer initialized successfully');
    } catch (error) {
        printerConnected = false;
        console.log('⚠️ Printer not detected:', error.message);
    }
}

// Initialize printer on startup
initializePrinter();

// Retry every 10 seconds if disconnected
setInterval(() => {
    if (!printerConnected) {
        console.log('🔄 Retrying printer connection...');
        initializePrinter();
    }
}, 10000);

const app = express();
app.use(bodyParser.json());

// Enable CORS for Laravel integration
app.use((req, res, next) => {
    res.header('Access-Control-Allow-Origin', '*');
    res.header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    res.header('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Authorization');
    if (req.method === 'OPTIONS') {
        res.sendStatus(200);
    } else {
        next();
    }
});

/**
 * Health check endpoint
 */
app.get('/health', (req, res) => {
    res.json({
        status: printerConnected ? 'OK' : 'WARNING',
        service: 'Thermal Printer Service',
        printer: 'OC-PT210',
        printer_connected: printerConnected,
        message: printerConnected ? 'Printer ready' : 'Printer not detected',
        timestamp: new Date().toISOString()
    });
});

/**
 * Manual reconnect endpoint
 */
app.post('/reconnect-printer', (req, res) => {
    initializePrinter();
    res.json({
        success: printerConnected,
        message: printerConnected ? '✅ Printer reconnected' : '❌ Printer still not found',
        printer_connected: printerConnected
    });
});

/**
 * Print receipt endpoint
 */
app.post('/print', (req, res) => {
    const { name, service, tracking, queue_no, date, time } = req.body;

    if (!name || !service || !tracking || !queue_no) {
        return res.status(400).json({
            success: false,
            error: 'Missing required fields: name, service, tracking, queue_no'
        });
    }

    if (!printerConnected) {
        return res.status(503).json({
            success: false,
            error: 'Printer not connected. Please reconnect.',
            printer_connected: false
        });
    }

    try {
        device.open(function (error) {
            if (error) {
                console.error('Printer connection error:', error);
                printerConnected = false;
                return res.status(500).json({
                    success: false,
                    error: 'Failed to connect to printer: ' + error.message,
                    printer_connected: false
                });
            }

            printer
                .align('ct')
                .style('b')
                .size(1, 1)
                .text('ALAMINOS CITY')
                .text('CLICK2SERVE')
                .text('========================')
                .align('lt')
                .style('normal')
                // No size setting = smallest font
                .text('')
                .text('  QUEUE NUMBER:')
                .align('ct')
                .style('b')
                .size(2, 2)  // Large, bold for queue number
                .text(`#${queue_no}`)
                .align('lt')
                .style('normal')
                // Back to smallest font (no size setting)
                .text('')
                .text('TRACKING')
                .text('NUMBER:')
                .align('ct')
                .style('b')
                .size(1, 1)  // Medium, bold for tracking number
                .text(tracking)
                .align('lt')
                .style('normal')
                // Back to smallest font (no size setting)
                .text('')
                .text('================')
                .text(`Name: ${name}`)
                .text(`Service: ${service}`)
                .text(`Date: ${date || new Date().toLocaleDateString()}`)
                .text(`Time: ${time || new Date().toLocaleTimeString()}`)
                .text('================')
                .align('ct')
                .text('Please keep this slip')
                .text('for tracking')
                .text('purposes')
                .text('')
                .text('Thank you!')
                .text('')
                .cut()
                .close(function () {
                    console.log('🖨️ Print job completed successfully');
                    res.json({
                        success: true,
                        message: 'Printed successfully',
                        printed_at: new Date().toISOString(),
                        printer_connected: true
                    });
                });
        });
    } catch (error) {
        console.error('Print error:', error);
        printerConnected = false;
        res.status(500).json({
            success: false,
            error: 'Print failed: ' + error.message,
            printer_connected: false
        });
    }
});

/**
 * Test print endpoint
 */
app.post('/test-print', (req, res) => {
    if (!printerConnected) {
        return res.status(503).json({
            success: false,
            error: 'Printer not connected.',
            printer_connected: false
        });
    }

    try {
        device.open(function (error) {
            if (error) {
                printerConnected = false;
                return res.status(500).json({
                    success: false,
                    error: 'Failed to connect: ' + error.message,
                    printer_connected: false
                });
            }

            printer
                .align('ct')
                .style('b')
                .size(1, 1)
                .text('THERMAL PRINTER TEST')
                .text('OC-PT210')
                .text('========================')
                .align('lt')
                .style('normal')
                // No size setting = smallest font
                .text('')
                .text('QUEUE NUMBER:')
                .align('ct')
                .style('b')
                .size(2, 2)  // Large, bold for queue number
                .text('#99')
                .align('lt')
                .style('normal')
                // Back to smallest font (no size setting)
                .text('')
                .text('TRACKING NUMBER:')
                .align('ct')
                .style('b')
                .size(1, 1)  // Medium, bold for tracking number
                .text('TEST-20240115-ABC123')
                .align('lt')
                .style('normal')
                // Back to smallest font (no size setting)
                .text('')
                .text('========================')
                .text('This is a test print')
                .text('If you can read this, your printer works!')
                .text('')
                .text(`Test Time: ${new Date().toLocaleString()}`)
                .text('========================')
                .cut()
                .close(function () {
                    res.json({
                        success: true,
                        message: 'Test print completed successfully',
                        printer_connected: true
                    });
                });
        });
    } catch (error) {
        printerConnected = false;
        res.status(500).json({
            success: false,
            error: 'Test print failed: ' + error.message,
            printer_connected: false
        });
    }
});

/**
 * Error handling middleware
 */
app.use((error, req, res, next) => {
    console.error('Server error:', error);
    res.status(500).json({
        success: false,
        error: 'Internal server error'
    });
});

const PORT = process.env.PORT || 4000;

app.listen(PORT, () => {
    console.log('==========================================');
    console.log('Thermal Printer Service Started');
    console.log('==========================================');
    console.log(`Server running on http://localhost:${PORT}`);
    console.log('Printer: OC-PT210 (Officom)');
    console.log(`Printer Status: ${printerConnected ? 'Connected' : 'Not Connected'}`);
    console.log('Endpoints:');
    console.log('   GET  /health - Health check');
    console.log('   POST /print - Print receipt');
    console.log('   POST /test-print - Test printer');
    console.log('   POST /reconnect-printer - Manual reconnect');
    console.log('==========================================');
});
