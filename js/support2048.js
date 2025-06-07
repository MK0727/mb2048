documentWith = window.screen.availWidth < window.screen.availHeight ? window.screen.availWidth:window.screen.availHeight;
gridContainerWidth = 0.79 * documentWith;
cellSideLength = 0.16 * documentWith;
cellSpace = 0.03 * documentWith;

function getPosTop(i, j){
	return cellSpace+i*(cellSpace + cellSideLength);
}

function getPosLeft(i, j){
	return cellSpace+j*(cellSpace + cellSideLength);
}

function getNumberBackgroundColor(number){

	switch(number){
		case 2: return "#eee4da"; break;
		case 4: return "#ede0c8"; break;
		case 8: return "#f2b179"; break;
		case 16: return "#f59563"; break;
		case 32: return "#f07c5f"; break;
		case 64: return "#ff5e3b"; break;
		case 128: return "#edcf72"; break;
		case 256: return "#fd0361"; break;
		case 512: return "#9c0"; break;
		case 1024: return "#33b5e5"; break;
		case 2048: return "#09c"; break;
		case 4096: return "#a6c"; break;
		case 8192: return "#93c"; break;
		case 16384: return "#888"; break;
		default: return "#111";break;
	}

	return "black";
}


function getTextValue(number){
	switch(number){
		case 2: return "挑战"; break;
		case 4: return "蝴蝶夫人"; break;
		case 8: return "平行线"; break;
		case 16: return "说谎动物"; break;
		case 32: return "跃入星光"; break;
		case 64: return "小云"; break;
		case 128: return "淬炼"; break;
		case 256: return "梦返"; break;
		case 512: return "盲选"; break;
		case 1024: return "打开"; break;
		case 2048: return "香魔星"; break;
		case 4096: return "光之黎明"; break;
		case 8192: return "没语季节"; break;
		case 16384: return "玫瑰星云"; break;
		case 16384: return "回音如果"; break;
		default: return "END";break;
	}
	return "black";
}

function getNumberColor(number){
	if(number <= 4){
		return "white";
	}
	return "white";
}

function nospace(board){
	for(var i=0; i<4; i++){
		for(var j=0; j<4; j++){
			if(board[i][j] == 0){
				return false;
			}
		}
	}
	return true;
}

function canMoveLeft(board){
	for(var i=0; i<4; i++){
		for(var j=1; j<4; j++){
			if(board[i][j] != 0){
				if(board[i][j-1] == 0
					|| board[i][j-1] == board[i][j]){
					return true;
			}
		}
	}
}
return false;
}
function canMoveRight(board){
	for(var i=0; i<4; i++){
		for(var j=2; j>=0; j--){
			if(board[i][j] != 0){
				if(board[i][j+1] == 0
					|| board[i][j+1] == board[i][j]){
					return true;
			}
		}
	}
}
return false;
}

function canMoveUp(board){
	for(var j=0; j<4; j++){
		for(var i=1; i<4; i++){
			if(board[i][j] != 0){
				if(board[i-1][j] == 0
					|| board[i-1][j] == board[i][j]){
					return true;
			}
		}
	}
}
return false;
}

function canMoveDown(board){
	for(var j=0; j<4; j++){
		for(var i=2; i>=0; i--){
			if(board[i][j] != 0){
				if(board[i+1][j] == 0
					|| board[i+1][j] == board[i][j]){
					return true;
			}
		}
	}
}
return false;
}

function noBlockHorizontal(row, col1, col2, board){
	for(var i=col1+1; i<col2; i++){
		if(board[row][i] != 0){
			return false;
		}
	}
	return true;
}
function noBlockVertical(row1, row2, col, board){
	for(var i=row1+1; i<row2; i++){
		if(board[i][col] != 0){
			return false;
		}
	}
	return true;
}

function noMove(board){
	if(canMoveDown(board)
		||canMoveUp(board)
		||canMoveRight(board)
		||canMoveLeft(board)){
		return false;
}
return true;
}

function uploadGrade(){
	
	
}

// 随机图标绑定缓存（全局只生成一次）
const tileImageMap = {};

function getTileImage(number) {
  // 保证一个值只绑定一次图标，但图标不重复
  if (!tileImageMap[number]) {
    let randomIndex;
    do {
      randomIndex = Math.floor(Math.random() * 210) + 1;
    } while (Object.values(tileImageMap).includes(`imgs/${randomIndex}.png`));
    tileImageMap[number] = `imgs/${randomIndex}.png`;
  }
  return tileImageMap[number];
}

