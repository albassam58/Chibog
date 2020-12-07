var server = require('http').Server();

var Redis = require('ioredis');
var redis = new Redis();

// Create a new Socket.io instance
var io = require('socket.io')(server, {
	'cors': {
		'methods': ['GET', 'PATCH', 'POST', 'PUT'],
		'origin': true // accept from any domain
	}
});

// redis.psubscribe('*');

// redis.on('pmessage', function (pattern, channel, data) {
//     data = JSON.parse(data);

//     // Pass data to Socket.io every time we get a new data from Redis
//     // "channel + ':' + data.event" is a unique channel id to broadcast to
//     //
//     // data.data corresponds to the $data variable in the dataSent event
//     // in Laravel
//     io.emit(channel + ':' + data.event, data.data);
// });

server.listen(3001);