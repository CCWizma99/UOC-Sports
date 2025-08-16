function changeYear(year) {
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.set('year', year);
    window.location.href = currentUrl.toString();
}

function showChart(type) {
    const barChart = document.getElementById('barChart');
    const lineChart = document.getElementById('lineChart');
    const buttons = document.querySelectorAll('.chart-type-btn');
    
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    if (type === 'bar') {
        lineChart.style.display = 'none';
        barChart.style.display = 'block';
        
        const bars = document.querySelectorAll('.bar');
        bars.forEach((bar, index) => {
            bar.style.animation = 'none';
            setTimeout(() => {
                bar.style.animation = `growUp 1s ease forwards`;
                bar.style.animationDelay = `${index * 0.1}s`;
            }, 10);
        });
    } else {
        barChart.style.display = 'none';
        lineChart.style.display = 'block';
        
        const linePath = document.querySelector('.line-path');
        const areaPath = document.querySelector('.area-path');
        
        if (linePath) {
            linePath.style.animation = 'none';
            setTimeout(() => {
                linePath.style.animation = 'drawLine 2s ease-in-out forwards';
            }, 10);
        }
        
        if (areaPath) {
            areaPath.style.animation = 'none';
            setTimeout(() => {
                areaPath.style.animation = 'fadeIn 2s ease-in-out 1s forwards';
            }, 10);
        }
    }
}