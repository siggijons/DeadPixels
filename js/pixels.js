var ws;
var ctx;
var boardSize = 400;
var column;

var init = function() 
{
	initBoard();
	connect();
};

var initBoard = function()
{
	var example = document.getElementById('board_canvas');
	ctx = example.getContext('2d');
	
	ctx.strokeStyle  = "rgba(0,0,0,0.5)";
	ctx.strokeRect(0, 0, boardSize, boardSize);
	
	ctx.lineWidth = 1;
	
			
	$("#board_canvas").click(function(e){
		if (column > 0)
		{
			var x = Math.floor((e.pageX-$("#board_canvas").offset().left) / column);
			var y = Math.floor((e.pageY-$("#board_canvas").offset().top) / column);
			ws.send(JSON.stringify({
				'action': 'insert',
				'x': x,
				'y': y,
				'color' : random_color(),
				'boardId' : 1
			}));
		}
	});
}

function random_color()
{
	var rint = Math.round(0xffffff * Math.random());
	return ('#0' + rint.toString(16)).replace(/^#0([0-9a-f]{6})$/i, '$1');
}

function drawBoard(board)
{
	size = board['size'];
	column = boardSize/size;
	for (i = 0; i < boardSize; i+=column)
	{
		ctx.beginPath();
		ctx.moveTo(0,i);
		ctx.lineTo(boardSize,i)
		ctx.stroke();
		
		ctx.beginPath();
		ctx.moveTo(i,0);
		ctx.lineTo(i, boardSize)
		ctx.stroke();
	}
	
	for(i in board['pixels'])
	{
		drawPixel(board['pixels'][i]);
	}
}

function drawPixel(pixel)
{
	console.log('Drawing pixel', pixel);
	x = pixel['x'];
	y = pixel['y'];
	ctx.fillStyle = '#' + pixel['color'];
	ctx.fillRect(x * column, y * column, column, column);
}

var connect = function() 
{
	if (window["WebSocket"]) 
	{
		ws = new WebSocket("ws://localhost:12345");
		ws.onopen = function() {
			ws.send(JSON.stringify({
				'action': 'board',
				'boardId' : 1
			}));
		};
		ws.onmessage = function(evt) 
		{
			data = evt.data.replace('\0','');
			msg = JSON.parse(data);
			if(msg['action'] == 'board')
				drawBoard(msg['data']);
			else if (msg['action'] == 'pixel')
				drawPixel(msg['data']);
		};
	}
	else {
		alert('no websockets for you my friend');
	}
};

window.onload = init;