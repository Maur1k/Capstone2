const escpos = require('escpos');
escpos.USB = require('escpos-usb');

console.log('🔍 Thermal Printer Troubleshooting Tool');
console.log('=====================================');

try {
    console.log('\n1. Checking for available USB devices...');
    const devices = escpos.USB.findPrinter();
    console.log('Available devices:', devices);
    
    if (devices && devices.length > 0) {
        console.log(`\n✅ Found ${devices.length} printer(s):`);
        devices.forEach((device, index) => {
            console.log(`   ${index + 1}. ${JSON.stringify(device)}`);
        });
        
        console.log('\n2. Attempting to connect to first printer...');
        const device = new escpos.USB(devices[0]);
        const printer = new escpos.Printer(device);
        
        console.log('✅ Printer object created successfully');
        
        console.log('\n3. Testing printer connection...');
        device.open(function(error) {
            if (error) {
                console.log('❌ Connection failed:', error.message);
            } else {
                console.log('✅ Printer connected successfully!');
                
                console.log('\n4. Testing print...');
                printer
                    .align('ct')
                    .style('b')
                    .text('PRINTER TEST')
                    .text('OC-PT210')
                    .text('===============')
                    .align('lt')
                    .text('If you can read this,')
                    .text('your printer is working!')
                    .text('')
                    .text(`Test Time: ${new Date().toLocaleString()}`)
                    .cut()
                    .close(function() {
                        console.log('✅ Test print completed successfully!');
                        process.exit(0);
                    });
            }
        });
        
    } else {
        console.log('❌ No printers detected');
        console.log('\nTroubleshooting steps:');
        console.log('1. Make sure your OC-PT210 is connected via USB');
        console.log('2. Check Windows Device Manager for the printer');
        console.log('3. Try running this script as Administrator');
        console.log('4. Install printer drivers if needed');
    }
    
} catch (error) {
    console.log('❌ Error:', error.message);
    console.log('\nPossible solutions:');
    console.log('1. Run as Administrator');
    console.log('2. Check USB permissions');
    console.log('3. Install node-usb drivers');
    console.log('4. Try a different USB port');
}






