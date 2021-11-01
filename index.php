<!DOCTYPE html>
<html>
    <head>

<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1, user-scalable=no">

		<title>JSMagneticPoetry</title>
    
        <style>

            * { margin: 0; }

            body {
                overscroll-behavior: contain;
            }

            .center { 
                position: absolute; 
                left: 50%; top: 50%; 
                -webkit-transform: translate(-50%, -50%); 
                transform: translate(-50%, -50%); 
                text-align: center;
            }

            #canvas {
                border: 1px solid black;
            }

            html, body {
                user-select: none; 
                -moz-user-select: none;
            }

        </style>
    
    
    
    </head>
    <body>
    
    <div class="center">
    
        <h1>JS Magnetic Poetry</h1>
    
        <canvas id="canvas"></canvas>

        <div style="float:right;">

            <button onclick="save()">Save</button>

            <button onclick="load()">Load</button>


            <button onclick="newWords()">New Words</button>
    
            <button onclick="removeSelectedWord()">Remove Selected Word</button>

            <button onclick="removeUnusedWords()">Remove Unused Words</button>

            <button onclick="removeUsedWords()">Remove Used Words</button>

        </div>

        <div style="float: clear;"></div>

        <div id="output"></div>

    </div>

    <script>



var wordlist=['&','&','a','a','a','a','a','a','about','above','ache','ad','after','all','am','am','an','an','and','and','and','and','apparatus','are','are','arm','as','as','as','as','ask','at','at','at','away','bare','be','beat','beauty','bed','beneath','bitter','black','blood','blow','blue','boil','boy','breast','but','but','but','but','butt','by','by','can','chant','chocolate','cool','could','crush','cry','d','day','death','delirious','diamond','did','do','do','dream','dress','drive','drool','drunk','eat','ed','ed','ed','ed','egg','elaborate','enormous','er','es','est','fast','feet','fiddle','finger','fluff','for','forest','frantic','friend','from','from','garden','girl','go','goddess','gorgeous','gown','hair','has','have','have','he','he','head','heave','her','her','here','him','his','his','honey','hot','how','I','I','I','I','if','in','in','in','ing','ing','ing','ing','ing','ing','is','is','is','is','is','it','it','it','juice','lake','language','languid','lather','lazy','less','let','lick','lie','life','light','like','like','like','live','love','luscious','lust','ly','ly','ly','ly','mad','man','me','me','me','mean','meat','men','milk','mist','moan','moon','mother','music','must','my','my','my','need','never','no','no','not','not','of','of','of','of','on','on','one','or','our','over','pant','peach','petal','picture','pink','play','please','pole','pound','puppy','purple','put','r','r','rain','raw','recall','red','repulsive','rip','rock','rose','run','rust','s','s','s','s','s','s','s','s','s','s','s','sad','said','sausage','say','scream','sea','see','shadow','she','she','shine','ship','shot','show','sing','sit','skin','sky','sleep','smear','smell','smooth','so','soar','some','sordid','spray','spring','still','stop','storm','suit','summer','sun','sweat','sweet','swim','symphony','the','the','the','the','the','their','there','these','they','those','though','thousand','through','time','tiny','to','to','to','together','tongue','trudge','TV','ugly','up','urge','us','use','want','want','was','watch','water','wax','we','we','were','what','when','whisper','who','why','will','wind','with','with','woman','worship','y','y','y','y','yet','you','you','you','you'];

var words=[];
var isDragging=false;
var dragIndex=-1;
var selectedIndex=-1;
var canvas;
var ctx;

// Set up mouse events for drawing
var drawing = false;
var mousePos = { x:0, y:0 };
var lastPos = mousePos;
var isMouseDown=false;



function getTextBBox( ctx, text ) { 

    const metrics = ctx.measureText( text ); 
    const left = metrics.actualBoundingBoxLeft * -1; 
    const top = metrics.actualBoundingBoxAscent * -1; 
    const right = metrics.actualBoundingBoxRight; 
    const bottom = metrics.actualBoundingBoxDescent; 
    // actualBoundinBox... excludes white spaces 
    const width = text.trim() === text ? right - left : metrics.width; 
    const height = bottom - top; 
    return { left, top, right, bottom, width, height }; 

}


function rnd(n) {
    return Math.floor(Math.random()*n);
} 


function inrect(x,y,rx,ry,rw,rh) {
    return x>=rx && x<=rx+rw && y>=ry && y<=ry+rh;
}



function Word(text,x,y) {

    this.text=text;
    this.x=x;
    this.y=y;
    this.diffx=0;
    this.diffy=0;    
    this.isPicked=false;
    this.isDrag=false;
    this.color="#ffffff";

    this.draw=function(ctx) {
    
        var bb=getTextBBox(ctx,this.text);
        
        var pad=16;
        var w=bb.width+pad;
        var h=bb.height+pad;
        
        ctx.fillStyle=this.color;  
        ctx.fillRect(this.x,this.y,w,h);
        ctx.strokeStyle="#000000";
        ctx.strokeRect(this.x,this.y,w,h);

        ctx.fillStyle="#000000";
        ctx.fillText(this.text,this.x+pad/2,this.y+pad/2);      
    }

}



function draw() {

    ctx.fillStyle="#FFFFFF";
    ctx.fillRect(0,0,canvas.width,canvas.height);
    
    ctx.font='16px sans-serif';
    ctx.textAlign="left";
    ctx.textBaseline="top";

    for(word of words) {
        word.draw(ctx);
    }
    


        if(isMouseDown) {

           
        
                if(isDragging) {
            
                    words[dragIndex].x=mousePos.x+words[dragIndex].diffx;
                    words[dragIndex].y=mousePos.y+words[dragIndex].diffy;
                
                    if(words[dragIndex].x<0) words[dragIndex].x=0;
                    if(words[dragIndex].y<0) words[dragIndex].y=0;
                    if(words[dragIndex].x>canvas.width-w) words[dragIndex].x=canvas.width-w;
                    if(words[dragIndex].y>canvas.height-h) words[dragIndex].y=canvas.height-h;
                } else {
                
                    var found=false;
                
                    for(var i=words.length-1;i>=0;i--) {
        
                        var bb=getTextBBox(ctx,words[i].text);
                
                        var pad=16;
                        var w=bb.width+pad;
                        var h=bb.height+pad;
               
                        if(inrect(mousePos.x,mousePos.y,words[i].x,words[i].y,w,h)) {
        
                            found=true;
                    
                            isDragging=true; 
 
 
                            if(selectedIndex!=-1) {
                                words[selectedIndex].color="#ffffff";
                                selectedIndex=-1;
                            }
 
                            
                            words[i].isPicked=true;
                            words[i].diffx=words[i].x-mousePos.x;
                            words[i].diffy=words[i].y-mousePos.y;
                            words[i].color="#d0d0d0";
                        
                            var tmp=words[i];
                            words[i]=words[words.length-1];
                            words[words.length-1]=tmp;
                            dragIndex=words.length-1;
                            selectedIndex=dragIndex;
                            break;
                        }
                
                     }
 
                     if(!found) {
                         if(selectedIndex!=-1) {
                             words[selectedIndex].color="#ffffff";
                             selectedIndex=-1;
                          }
                     }                                        
                }
            
        } else {        
            isDragging=false;
        }
        
    
}



function newWords() {

    if(selectedIndex!=-1) {
        words[selectedIndex].color="#ffffff";
        selectedIndex=-1;
    }


    var gap=8;
    var xs=canvas.width/2+gap,ys=gap;
    var x=xs,y=ys;

    var i = 0; 
    while(i<words.length) { 
        if(!words[i].isPicked) { 
            words.splice(i,1); 
        } else { 
            ++i; 
        } 
    }

    for(;;) {
    
        var word=wordlist[rnd(wordlist.length)];
        
        var bb=getTextBBox(ctx,word);
        
        var pad=16;
        var w=bb.width+pad;
        var h=bb.height+pad;
    
        if(x+w+gap>=canvas.width) {
            x=xs;
            y+=h+gap;
            if(y+h+gap>=canvas.height) break;
        }
    
        words.push(new Word(word,x,y));
    
        x+=w+gap;
        
    }

}


function removeSelectedWord() {
    if(selectedIndex!=-1) {
        words.splice(selectedIndex,1);
        selectedIndex=-1;
    }
    dragIndex=-1;
}

function removeUnusedWords() {

    if(selectedIndex!=-1) {
        words[selectedIndex].color="#ffffff";
        selectedIndex=-1;
    }
    dragIndex=-1;

    var i = 0; 
    while(i<words.length) { 
        if(!words[i].isPicked) { 
            words.splice(i,1); 
        } else { 
            ++i; 
        } 
    }
}

function removeUsedWords() {
    if(selectedIndex!=-1) {
        words[selectedIndex].color="#ffffff";
        selectedIndex=-1;
    }
    dragIndex=-1;
    var i = 0; 
    while(i<words.length) { 
        if(words[i].isPicked) { 
            words.splice(i,1); 
        } else { 
            ++i; 
        } 
    }
}

function save() {
    if(selectedIndex!=-1) {
        words[selectedIndex].color="#ffffff";
        selectedIndex=-1;
    }
    dragIndex=-1;

    var data=[];

    for(word of words) {
        if(word.isPicked) data.push({text:word.text,x:word.x,y:word.y});
    }

    var xhttp = new XMLHttpRequest();
    xhttp.open("POST","set.php");
    xhttp.setRequestHeader("Content-Type","application/json");
    xhttp.onreadystatechange = function() {
        alert(this.responseText);
    };
    xhttp.send(JSON.stringify(data));


}

function load() {
    if(selectedIndex!=-1) {
        words[selectedIndex].color="#ffffff";
        selectedIndex=-1;
    }
    dragIndex=-1;

    var id=prompt("enter poem id",1);

    var xhttp = new XMLHttpRequest();
    xhttp.open("GET","get.php?id="+id);
    xhttp.onreadystatechange = function() {
        var data=JSON.parse(this.responseText); 
        words=[];
        for(d of data) {
            var w=new Word(d.text,d.x,d.y);
            w.isPicked=true;
            words.push(w);
        }
    };
    xhttp.send();


}

    canvas = document.getElementById("canvas");
    ctx = canvas.getContext("2d");

    canvas.addEventListener("mousedown", function (e) {
        drawing = true;
        isMouseDown=true;
        lastPos = getMousePos(canvas, e);
    }, false);

    canvas.addEventListener("mouseup", function (e) {
        drawing = false;
        isMouseDown=false;
    }, false);

    canvas.addEventListener("mousemove", function (e) {
        mousePos = getMousePos(canvas, e);
    }, false);

    // Set up touch events for mobile, etc
    canvas.addEventListener("touchstart", function (e) {
        mousePos = getTouchPos(canvas, e);
        var touch = e.touches[0];
        var mouseEvent = new MouseEvent("mousedown", {
            clientX: touch.clientX,
            clientY: touch.clientY
        });
        canvas.dispatchEvent(mouseEvent);
    }, false);

    canvas.addEventListener("touchend", function (e) {
        var mouseEvent = new MouseEvent("mouseup", {});
        canvas.dispatchEvent(mouseEvent);
    }, false);

    canvas.addEventListener("touchmove", function (e) {
        var touch = e.touches[0];
        var mouseEvent = new MouseEvent("mousemove", {
            clientX: touch.clientX,
            clientY: touch.clientY
        });
        canvas.dispatchEvent(mouseEvent);
    }, false);

    // Prevent scrolling when touching the canvas
    document.body.addEventListener("touchstart", function (e) {
        if (e.target == canvas) {
            e.preventDefault();
        }
    }, false);

    document.body.addEventListener("touchend", function (e) {
        if (e.target == canvas) {
            e.preventDefault();
        }
    }, false);

    document.body.addEventListener("touchmove", function (e) {
        if (e.target == canvas) {
            e.preventDefault();
        }
    }, false);

    // Get the position of the mouse relative to the canvas
    function getMousePos(canvasDom, mouseEvent) {
        var rect = canvasDom.getBoundingClientRect();
        return {
            x: mouseEvent.clientX - rect.left,
            y: mouseEvent.clientY - rect.top
        };
    }

    // Get the position of a touch relative to the canvas
    function getTouchPos(canvasDom, touchEvent) {
        var rect = canvasDom.getBoundingClientRect();
        return {
            x: touchEvent.touches[0].clientX - rect.left,
            y: touchEvent.touches[0].clientY - rect.top
        };
    }




    
    canvas.width=640;
    canvas.height=480;

    ctx.font='16px sans-serif';
    ctx.textAlign="left";
    ctx.textBaseline="top";

    newWords();

    setInterval(draw,1000/60);




    </script>

    </body>
</html>