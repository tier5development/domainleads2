var canvasObj = {
    canvas : null,
    context : null,
    counter : 0,
    av : 0,
    start : 4.72,
    cw : 0, // canvas width
    ch : 0, // canvas height
    diff : 0,
    targetVal : 0,
    currentVal : 0,
    radius : 0,
    chartRatio : 0,
    gradient : null,
    previousModel: null,
    previousStartPoint : null,
    previousCounter : null,
    stepRatio : 0,
    setCanvas : function() {
        
        this.canvas = document.getElementById('crart'); 
        this.context = this.canvas.getContext('2d');
        this.counter = this.counter > 0 ? this.counter : 0;
        this.av = 0;
        this.start = 4.72;
        this.cw = this.context.canvas.width/2;
        this.ch = this.context.canvas.height/2;
        this.diff = 0;
    
    }, setCurve : function(current, target) {
        
        this.targetVal = target;
        this.currentVal = current == undefined ? 0 : current;
        this.radius = 60;
        this.chartRatio = (this.currentVal / this.targetVal) * 100;
        this.gradient = this.context.createLinearGradient(0, 0, 0, 140);
        this.gradient.addColorStop(0, '#48e4b3');
        this.gradient.addColorStop(0.5, '#3cbec1');
        this.gradient.addColorStop(1, '#48e4b3');
    
    }, change : function(current, interval) {
        
        this.currentVal = current;
        this.chartRatio = (this.currentVal / this.targetVal) * 100;
        this.stepRatio = 1/this.targetVal * 100;
        this.previousModel = setInterval(this.drawCanvasCustom.bind(this), interval);
    
    }, drawProgressBar : function(interval) {

        this.previousModel = setInterval(this.drawCanvasCustom.bind(this), interval);

    }, drawCanvasCustom : function() {
        
        this.diff = (this.counter/100)*Math.PI*2;
        this.context.clearRect(0,0,400,400);
        this.context.beginPath();
        this.context.lineWidth      =   10;
        this.context.arc(this.cw, this.ch, this.radius, 0, 2*Math.PI, false);
        this.context.fillStyle      =   '#FFF';
        this.context.fill();
        this.context.strokeStyle    =   '#f6f6f6';
        this.context.stroke();

        this.context.fillStyle      =   '#000';
        this.context.strokeStyle    =   this.gradient;
        this.context.textAlign      =   'center';
        this.context.lineWidth      =   10;
        this.context.font           =   '21px "Avenir LT Std 95 Black"';
        this.context.fillStyle      =   '#333';
        if(this.diff + this.start > this.start) {
            this.context.beginPath();
            this.context.arc(this.cw, this.ch, this.radius, this.start, (this.diff + this.start), false);
            this.context.stroke();
        }
        // console.log('stroke done:');
        this.context.lineCap        =   'round';
        this.context.fillText(this.currentVal + "/" + this.targetVal, 65, 75);
        
        
        // console.log(this.cw, this.ch, this.radius, this.start, (this.diff + this.start), false);
        if(this.counter >= this.chartRatio) {
            this.start = this.diff + this.start;
            clearTimeout(this.previousModel);
        } else {
            this.counter++;
            this.av++;
            if(this.av >= this.currentVal) {
                this.av = this.currentVal;
            }
        }
    }, refresh : function(interval) {
        this.start = 4.72;
        this.counter = 0;
        this.previousModel = setInterval(this.drawCanvasCustom.bind(this), interval);
    }
}
