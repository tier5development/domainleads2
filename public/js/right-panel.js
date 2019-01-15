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
    setCanvas : function() {
        
        this.canvas = document.getElementById('crart'); 
        this.context = this.canvas.getContext('2d');
        console.log('context set', this.context);
        this.counter = 0;
        this.av = 0;
        this.start = 4.72;
        this.cw = this.context.canvas.width/2;
        this.ch = this.context.canvas.height/2;
        this.diff = 0;
    
    }, setCurve : function(current, target) {
        console.log('current : '. current, 'target : ', target);
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
        this.previousModel = setInterval(this.drawCanvasCustom.bind(this), interval);
    
    }, drawProgressBar : function(interval) {
        
        this.previousModel = setInterval(this.drawCanvasCustom.bind(this), interval);

    }, drawCanvasCustom : function() {

        this.diff = (this.counter/100)*Math.PI*2;
        this.context.clearRect(0,0,400,400);
        this.context.beginPath();
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
        this.context.beginPath();
        this.context.arc(this.cw, this.ch, this.radius, this.start, (this.diff + this.start), false);
        // console.log('context ref : ', this.cw, this.ch, this.radius, this.start, this.diff + this.start,  this.diff, 'counter = '+this.counter);
        this.context.stroke();
        this.context.lineCap        =   'round';
        this.context.fillText(this.currentVal + "/" + this.targetVal ,65 ,75);
        if(this.counter >= this.chartRatio) {
            clearTimeout(this.previousModel);
            // console.log('final context : ', this.context);
        }
        this.counter++;
        this.av++;
        if(this.av >= this.currentVal){
            this.av = this.currentVal;
        }
    }
}


// var canvas = document.getElementById('crart');
// var context = canvas.getContext('2d');
// var al=0;
// var av = 0;
// var start=4.72;
// var cw=context.canvas.width/2;
// var ch=context.canvas.height/2;
// var diff;

// var targetVal = 50;
// var currentVal = 40;

// var radius = 60;
// var completionRatio = (currentVal / targetVal) * 100;

// var gradient = context.createLinearGradient(0, 0, 0, 140);
//     gradient.addColorStop(0, '#48e4b3');
//     gradient.addColorStop(0.5, '#3cbec1');
//     gradient.addColorStop(1, '#48e4b3');

// function progressBar(){
//     console.log('I am called', al, completionRatio);
//     diff=(al/100)*Math.PI*2;
//     context.clearRect(0,0,400,400);
//     context.beginPath();
//     context.arc(cw,ch,radius,0,2*Math.PI,false);
//     context.fillStyle='#FFF';
//     context.fill();
//     context.strokeStyle='#f6f6f6';
//     context.stroke();
//     context.fillStyle='#000';
//     context.strokeStyle= gradient;
//     context.textAlign='center';
//     context.lineWidth=10;
//     context.font = '21px "Avenir LT Std 95 Black"';
//     context.fillStyle = '#333';
//     context.beginPath();
//     context.arc(cw,ch,radius,start,diff+start,false);
//     context.stroke();
//     context.lineCap = 'round';
//     context.fillText(av+"/50" ,65, 75 );
//     if(al>=completionRatio){
//         clearTimeout(bar);
//     }
//         al++;
//         av++;
//     if(av>=currentVal) {
//         av = currentVal;
//     }
// }

// var bar = setInterval(progressBar, 10);