/* CryptoX Pro Exchange - Frontend JS */
 
document.addEventListener('DOMContentLoaded', () => {
 
    // 1. Live Price Ticker Update (AJAX)
    const updateTicker = async () => {
        try {
            const response = await fetch('realtime.php');
            const data = await response.json();
 
            for (const coin in data) {
                const priceElement = document.getElementById(`${coin.toLowerCase()}-price`);
                const changeElement = document.getElementById(`${coin.toLowerCase()}-change`);
 
                if (priceElement) {
                    const oldPrice = parseFloat(priceElement.textContent.replace('$', '').replace(',', ''));
                    const newPrice = data[coin].raw_price;
 
                    priceElement.textContent = `$${data[coin].price}`;
 
                    // Flash effect on price change
                    if (newPrice > oldPrice) {
                        priceElement.style.color = '#00ff88';
                        setTimeout(() => priceElement.style.color = '', 500);
                    } else if (newPrice < oldPrice) {
                        priceElement.style.color = '#ff4d4d';
                        setTimeout(() => priceElement.style.color = '', 500);
                    }
                }
 
                if (changeElement) {
                    const change = parseFloat(data[coin].change);
                    changeElement.textContent = `${change > 0 ? '+' : ''}${data[coin].change}%`;
                    changeElement.className = change >= 0 ? 'price-up' : 'price-down';
                }
 
                // Update Trading View Price if active
                const activePriceEl = document.getElementById('active-coin-price');
                if (activePriceEl && document.getElementById('trading-coin-symbol')?.textContent.includes(coin)) {
                    activePriceEl.textContent = data[coin].price;
                }
            }
        } catch (error) {
            console.error('Error fetching live prices:', error);
        }
    };
 
    // Update every 5 seconds
    if (document.getElementById('liveTicker')) {
        setInterval(updateTicker, 5000);
    }
 
    // 2. Chart.js Implementation
    const ctx = document.getElementById('tradingChart');
    if (ctx) {
        const labels = Array.from({length: 30}, (_, i) => `${i + 1}:00`);
        const initialData = Array.from({length: 30}, () => Math.floor(Math.random() * 100) + 64000);
 
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'BTC/USDT',
                    data: initialData,
                    borderColor: '#f0b90b',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(240, 185, 11, 0.05)',
                    pointRadius: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { display: false },
                    y: {
                        grid: { color: 'rgba(255,255,255,0.05)' },
                        ticks: { color: '#848e9c' }
                    }
                }
            }
        });
 
        // Simulating live chart updates
        setInterval(() => {
            const newData = initialData.shift();
            const lastVal = chart.data.datasets[0].data[chart.data.datasets[0].data.length - 1];
            const newVal = lastVal + (Math.random() * 20 - 10);
 
            chart.data.datasets[0].data.push(newVal);
            chart.data.datasets[0].data.shift();
            chart.update('none');
        }, 5000);
    }
 
    // 3. Tab Switching (Dashboard)
    const tradeTabs = document.querySelectorAll('.trade-tab');
    if (tradeTabs) {
        tradeTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tradeTabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
 
                const type = tab.dataset.type;
                const buyBtn = document.getElementById('buy-btn');
                const sellBtn = document.getElementById('sell-btn');
 
                if (type === 'buy') {
                    buyBtn.style.display = 'block';
                    sellBtn.style.display = 'none';
                } else {
                    buyBtn.style.display = 'none';
                    sellBtn.style.display = 'block';
                }
            });
        });
    }
 
    // 4. Asset Allocation Chart
    const assetCtx = document.getElementById('assetAllocationChart');
    if (assetCtx) {
        new Chart(assetCtx, {
            type: 'doughnut',
            data: {
                labels: ['BTC', 'ETH', 'SOL', 'XRP', 'BNB'],
                datasets: [{
                    data: [45, 25, 15, 10, 5],
                    backgroundColor: ['#f7931a', '#627eea', '#14f195', '#23292f', '#f3ba2f'],
                    borderWidth: 0
                }]
            },
            options: {
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#eaecef' }
                    }
                }
            }
        });
    }
});
 
// Sidebar Toggle (Mobile)
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.classList.toggle('active');
    }
}
 
