'use strict';

var WallPostForm=React.createClass({

	getInitialState: function(){
		return { 
			user_update: '',
			attachment: '',
			isFormloading: false,
			attachmentIds: [],
			takephoto: false,
			takevideo: false,
			isTakePhotoLoading: false,
			isTakeVideoLoading: false,
			video: '',
			image: '',
			canvas: '',
			stream: '',
	  		actions: [
		  		{label: '', icon: 'photo', class: '', action: '_takePhoto'},
		  		{label: '', icon: 'record', class: '',  action: '_takeVideo'},
		  		{label: '', icon: 'attach', class: '',  action: '_addAttachment'}
	  		],
		};
	},
	_updateChange:function(event){
		var reactThis = this;

	    const target = event.target;
	    const value = target.type === 'checkbox' ? target.checked : target.value;
	    const name = target.name;

	    reactThis.setState({
	      [name]: value
	    });

	},
	_addAttachment: function(){
		var reactThis = this;
		var attachment = ReactDOM.findDOMNode(reactThis.refs['attachment']);
		attachment.click();
	},
	_attachment: function(event){
		const target = event.target;
		var reactThis = this;

		var attachment = ReactDOM.findDOMNode(reactThis.refs['attachment']);

		if(target.type == 'file'){

    		var files = event.target.files;

	    	for (var i = 0, f; f = files[i]; i++) {

	    		var div = document.createElement('div'),
		    		clnFile = attachment.cloneNode(true), 
		    		close = document.createElement('button'), 
		    		icon = document.createElement('i');

	    		close.setAttribute('type', 'button');
	    		icon.className="icon close";
	    		close.className="circular ui icon button attachment-close red";
	    		close.appendChild(icon);

	    		close.addEventListener("click", function() {
	    			div.remove();
	    		});

		      	// Only process image files.
		      	if (f.type.match('image.*')) {

	        		var reader = new FileReader();

					reader.onload = (function(fi) {

					return function(e) {

							div.innerHTML = 
							[
								'<img  src="', 
								e.target.result,
								'" title="', escape(fi.name), 
								'"/>'
							].join('');

							div.appendChild(close);
							div.appendChild(clnFile);
							div.className = "ui bordered image attachments";

							document.getElementById('attpreview').insertBefore(div, null);

							var fd = new FormData();
							fd.append('qqfilename', fi.name);
							fd.append('qquuid', Math.floor(Date.now() / 1000));	
							fd.append('qqfile', reactThis._toBlob(e.target.result));

							jQuery.ajax({
							    type: 'POST',
							    url:shiftwall.root + 'wp/v2/canvas',
							    data: fd,
							    processData: false,
							    contentType: false
							}).done(function(data) {
				                var attachment = reactThis.state.attachmentIds.slice();
							    attachment.push(data.ID);   
							    reactThis.setState({ attachmentIds : attachment });
							});

				        };

			      	})(f);

					reader.readAsDataURL(f);

				}else{

					var reader = new FileReader();

					reader.onload = (function(fi) {

						return function(e) {

							var div = document.createElement('div');
							div.innerHTML = 
							[
								'<span><i class="file text outline huge icon"></i></span>'
							].join('');
							
							div.appendChild(close);
							div.appendChild(clnFile);
							div.className = "ui bordered image attachments";
							
							document.getElementById('attpreview').insertBefore(div, null);

							var fd = new FormData();
							fd.append('qqfilename', fi.name);
							fd.append('qquuid', Math.floor(Date.now() / 1000));	
							fd.append('qqfile', reactThis._toBlob(e.target.result));

							jQuery.ajax({
							    type: 'POST',
							    url:shiftwall.root + 'wp/v2/canvas',
							    data: fd,
							    processData: false,
							    contentType: false
							}).done(function(data) {
				                var attachment = reactThis.state.attachmentIds.slice();
							    attachment.push(data.ID);   
							    reactThis.setState({ attachmentIds : attachment });
							});

						}

					})(f);

					reader.readAsDataURL(f);

	      		}

		    }
	  	}
	},
	_takePhoto: function(){

		var reactThis = this;
		var player = '';

		if(!reactThis.state.takephoto && typeof reactThis.state.stream == 'object')
			reactThis.state.stream.recorder.destroy();

		reactThis.setState({takevideo: false}, () => {

			reactThis.setState({takephoto: !reactThis.state.takephoto}, () => {

				if(reactThis.state.takephoto){
					
					// var video = ReactDOM.findDOMNode(reactThis.refs.takephoto);

					var player = videojs('photojs',{
					    controls: true,
					    width: 320,
					    height: 280,
					    plugins: {
					        record: {
								image: true,
								debug: true
					        }
					    }
					});

					reactThis.setState({stream: player}, () => {

						reactThis.state.stream.recorder.getDevice();

			            // data is available
			            reactThis.state.stream.on('finishRecord', function()
			            {

			            	var attachment = ReactDOM.findDOMNode(reactThis.refs['attachment']),
		            	 		clnFile = attachment.cloneNode(true), 
		            	 		close = document.createElement('button'), 
		            	 		icon = document.createElement('i'),
		            	 		inputBase64 = document.createElement('input'),
		            	 		div = document.createElement('div');
							
							//Base64encode image
							inputBase64.setAttribute('type', 'hidden');
							inputBase64.setAttribute('id', 'attachments');
							inputBase64.className = 'attachment';
							inputBase64.value = reactThis.state.stream.recordedData;

				    		icon.className="icon close";
				    		close.className="circular ui icon button attachment-close red";
				    		close.appendChild(icon);
	    					close.setAttribute('type', 'button');
		    				
							div.innerHTML = 
							[
								'<img  src="', 
									reactThis.state.stream.recordedData,
								// '" title="', escape(theFile.name),
								'" />'
							].join('');

							div.appendChild(close);
							div.appendChild(clnFile);
							div.className = "ui bordered image attachments";

							document.getElementById('attpreview').insertBefore(div, null);

				    		close.addEventListener("click", function() {
				    			div.remove();
				    		});

							var fd = new FormData();
							fd.append('qqfilename', Math.floor(Date.now() / 1000) + '.jpg');
							fd.append('qquuid', Math.floor(Date.now() / 1000));	
							fd.append('qqfile', reactThis._toBlob(reactThis.state.stream.recordedData));

							jQuery.ajax({
							    type: 'POST',
							    url:shiftwall.root + 'wp/v2/canvas',
							    data: fd,
							    processData: false,
							    contentType: false
							}).done(function(data) {
		              			// reset camera
				                reactThis.state.stream.recorder.getDevice();
				                var attachment = reactThis.state.attachmentIds.slice();
							    attachment.push(data.ID);   
							    reactThis.setState({ attachmentIds : attachment });
				                
							});

			            });

					});

				}else{

					var player = reactThis.state.stream.recorder;
					reactThis.setState({stream: ''}, () => {
						player.destroy();
					});

				}

			});

		}); //force to close if active
			
	},
	_takeVideo: function(){
		var reactThis = this;

		if(!reactThis.state.takevideo && typeof reactThis.state.stream == 'object')
			reactThis.state.stream.recorder.destroy();

		reactThis.setState({takephoto: false}, () => {

			// reactThis.setState({isTakeVideoLoading: true});

			reactThis.setState({takevideo: !reactThis.state.takevideo}, () => {

				if(reactThis.state.takevideo){

					var player = videojs('videojs',{
					    controls: true,
					    loop: false,
					    width: 320,
					    autoplay: true,
					    height: 280,
					    plugins: {
					        record: {
					            maxLength: 10,
					            debug: true,
					            audio: true,
					            autplay: true,
					            video: {
					                mandatory: {
					                    minWidth: 320,
					                    minHeight: 280,
					                },
					            },
					            frameWidth: 320,
					            frameHeight: 280
					        }
					    }
					});

					reactThis.setState({stream: player}, () => {

						reactThis.state.stream.recorder.getDevice();

			            // data is available
			            reactThis.state.stream.on('finishRecord', () => {


							var attachment = ReactDOM.findDOMNode(reactThis.refs['attachment']),
		            	 		clnFile = attachment.cloneNode(true), 
		            	 		close = document.createElement('button'), 
		            	 		icon = document.createElement('i'),
		            	 		icon2 = document.createElement('i'),
		            	 		inputBase64 = document.createElement('input'),
		            	 		video = document.createElement('video'),
		            	 		div = document.createElement('div');
							
							// video
							video.src = window.URL.createObjectURL(reactThis.state.stream.recordedData.video);
							video.style.width = '100%';
							icon2.className="icon circle play attachment-play";
							div.appendChild(icon2);
							div.appendChild(video);

							//Base64encode image
							inputBase64.setAttribute('type', 'hidden');
							inputBase64.setAttribute('id', 'attachments');
							inputBase64.className = 'attachment';
							inputBase64.value = reactThis.state.stream.recordedData;

				    		icon.className="icon close";
				    		close.className="circular ui icon button attachment-close red";
				    		close.appendChild(icon);
	    					close.setAttribute('type', 'button');
		    				
							div.appendChild(close);
							div.appendChild(clnFile);
							div.className = "ui bordered image video attachments";

							document.getElementById('attpreview').insertBefore(div, null);

				    		close.addEventListener("click", function() {
				    			div.remove();
				    		});
    
							var fd = new FormData();
							fd.append('qqfilename', Math.floor(Date.now() / 1000) + '.webm');
							fd.append('qquuid', Math.floor(Date.now() / 1000));	
							fd.append('qqfile', reactThis.state.stream.recordedData.video);

							jQuery.ajax({
							    type: 'POST',
							    url:shiftwall.root + 'wp/v2/canvas',
							    data: fd,
							    processData: false,
							    contentType: false
							}).done(function(data) {
							   	reactThis.state.stream.recorder.getDevice();
				                var attachment = reactThis.state.attachmentIds.slice();
							    attachment.push(data.ID);   
							    reactThis.setState({ attachmentIds : attachment });
							});

			            });

					});


				}else{

					var player = reactThis.state.stream.recorder;
					reactThis.setState({stream: ''}, () => {
						player.destroy();
					});

				}

			});

		}); //force to close if active

	},
	updateSubmit: function(e){
		e.preventDefault();
		var reactThis = this;
		var user_update= reactThis.state.user_update.trim();

		reactThis.setState({isFormloading: true});

		if(!user_update)
		{
			reactThis.setState({isFormloading: false});
	  		return;
		}
		else
		{

			reactThis.props.setFeedPost(
				'shift_wall',
				{'title': 'Activity ' + new Date(), actions: 'feed_create', '_shift_wall_attachment': reactThis.state.attachmentIds, 'content': reactThis.state.user_update, 'status': 'publish', 'time': new Date() },
				reactThis,
				function(resp){
					
					console.log('Saving Post!');

					reactThis.setState({attachmentIds: []});
					reactThis.setState({'user_update': ''});
					attpreview.innerHTML = '';
					reactThis.props._componentBridge(resp);
					//Set delay for hiding the loading form.
					setTimeout(function(){
						reactThis.setState({isFormloading: false});	
					}, 1000)
				}
			);
		}
	},
	_toBlob: function(dataURI){
		var byteString = atob(dataURI.split(',')[1]);

		  // separate out the mime component
		  var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0]

		  // write the bytes of the string to an ArrayBuffer
		  var ab = new ArrayBuffer(byteString.length);
		  var ia = new Uint8Array(ab);
		  for (var i = 0; i < byteString.length; i++) {
		      ia[i] = byteString.charCodeAt(i);
		  }

		  // write the ArrayBuffer to a blob, and you're done
		  var blob = new Blob([ab], {type: 'image/png'});
		  return blob;
	},
	_onAction: function(action, evnt){
		var reactThis = this;
		var fn = window[action];
		if(action=='_takePhoto'){
			reactThis._takePhoto();
		}else if(action=='_takeVideo'){
			reactThis._takeVideo();
		}else if(action=='_addAttachment'){
			reactThis._addAttachment();
		}else if(typeof fn === 'function') {
			window[action](reactThis.props.feed);
		}else{
			// window.location.href = action;
		}
	},
  	componentDidMount: function(){
	    // ReactDOM.findDOMNode(this.refs.updateInput).focus(); 
	    var feedform = ReactDOM.findDOMNode(this.refs.feedform);
	    feedform.setAttribute('enctype', 'multipart/form-data');

		// var feedpost = ReactDOM.findDOMNode(this.refs.feedpost);
		//    feedpost.setAttribute('style', 'min-height: 5rem;');
		//    feedpost.setAttribute('contenteditable', 'true');
		//    feedpost.setAttribute('data-ph', 'Write something here...');	

	},
	render:function(){
	  	var reactThis = this;
	  	
	  	var camera = function(){

			if(reactThis.state.takephoto){

				return(
					<div id="camera" className="ui eight wide column " >
						<div className={"ui segment camera-loading " + (!reactThis.state.isTakePhotoLoading ? 'hide' : '')}>
							<div className="ui active dimmer">
						    	<div className="ui small text loader">Loading</div>
							</div>
							<p></p>
						</div>
						<video id="photojs" ref="takephoto" className="video-js vjs-default-skin"></video>
						<button id="snap" type="button" className="green circular ui icon button ">Snap</button>

					</div>
				);
			}

			if(reactThis.state.takevideo){
				return(
					<div id="camera" className="ui eight wide column " >
						<div className={"ui segment camera-loading " + (!reactThis.state.isTakeVideoLoading ? 'hide' : '')}>
							<div className="ui active dimmer">
						    	<div className="ui small text loader">Loading</div>
							</div>
							<p></p>
						</div>
						<video id="videojs" ref="takevideo" className="video-js vjs-default-skin"></video>
						<button id="snap" type="button" className="green circular ui icon button ">Record</button>
					</div>
				);
			}
		}

		var postActions = reactThis.state.actions.map(function(option, index){
			return(
				<button type="button" id="take-photo"  className={"circular ui icon button " + option.class} onClick={reactThis._onAction.bind(reactThis, option.action, )}>
	        		<i className={"icon " + option.icon}></i> {option.label}
	  			</button>
			)
		}.bind(this));
	
	  	return(
			<div>
				<form className={"ui " + (reactThis.state.isFormloading ? 'loading': '') + " form"} ref="feedform" onSubmit={reactThis.updateSubmit} >
					<div id="form-wrap">
						<div className="field">
							<textarea id="feed-post" value={reactThis.state.user_update} rows="5" placeholder="Write something here..." name="user_update" onChange={reactThis._updateChange}></textarea>
						</div>
						<div id="form-actions" className="ui grid">

						    <div className="eight wide column">
					      		
					      		{postActions}

		


					    		<input className="attachment hide" ref="attachment" type="file" onChange={reactThis._attachment} />
						    </div>	

						    <div className="five wide column"></div>
						    <div className="three wide column">
						    	<button type="submit" className="ui submit button fluid primary">POST</button>
						    </div>
						</div>
					</div>
					
					<div id="form-attachment" className="ui grid">
						{camera()}
						<div id="attpreview" className={"ui small images " +  (this.state.takephoto ? 'eight' : ' eight') + " wide column" } ></div>
					</div>

					<input type="hidden" name="content"  />
					<input type='hidden' name="status" />
				</form>
			</div>
	  	);
	}
  
});


var WallCommentForm=React.createClass({

	getInitialState: function(){
		return { user_comment: ''};
	},
	_updateChange:function(e){
		this.setState({user_comment: this.refs.commentpost.innerHTML });
	},
	_updateSubmit: function(e){
		var reactThis = this;
		// e.preventDefault();
		if (e.charCode == 13) {
			e.preventDefault();
			var user_comment= reactThis.state.user_comment.trim();
			if(!user_comment)
			{
			  return;
			}
			else
			{
				reactThis.props.create(
					'comments',
					{
						'content': reactThis.refs.commentpost.innerHTML,
						'post': reactThis.props.fid,
						// 'status': 'publish', 
						'time': new Date() 
					},
					reactThis,
					function(){}
				);
				reactThis.refs.commentpost.innerHTML = "";
			}
		}
	},
  	componentDidMount: function(){
		var frmPost = ReactDOM.findDOMNode(this.refs.commentpost);
	    frmPost.setAttribute('contenteditable', 'true');
	    frmPost.setAttribute('data-ph', 'Write your comment here...');
  	},
	render:function(){
        return(
		   	<div className="ui reply form">
	      		<div className="field">
	            	<div id="feed-post" ref="commentpost" onInput={this._updateChange} onKeyPress={this._updateSubmit} ></div>
	      		</div>
	        </div>
        );
	}
});

var WallFeedActions=React.createClass({
	getInitialState: function() {
		return { 
	  		actions: [
		  		{label: 'Delete', action: "_deleteFeed"},
		  		{label: 'Edit', action: "_editFeed"},
		  		{label: 'Post Link', action: "_showPostLink"}
	  		],
		};
	},
	componentDidMount:function(){
		var reactThis = this;
		jQuery('.ui.dropdown').dropdown();
	},
	_deleteFeed: function(){
		var reactThis = this;
		reactThis.props.deleting(true, reactThis.props.index);
		reactThis.props.delete(
			'shift_wall/' + reactThis.props.feed.id,
			{actions: 'delete_feed'},
			reactThis,
			function(resp){
				reactThis.props.deleting(false, reactThis.props.index);
				// document.getElementById('feed-'+resp.id).remove();
			}
		);
	},
	_showPostLink: function(){
		var reactThis = this;
		window.prompt("Post Link", shiftwall.base_url + '?p=' + reactThis.props.feed.id);
	},
	_editFeed: function(){
		var reactThis = this;
		reactThis.props.editing(true, reactThis.props.index);
	},
	_onAction: function(action, evnt){
		var reactThis = this;
		var fn = window[action];
		if(action=='_deleteFeed'){
			reactThis._deleteFeed();
		}else if(action=='_showPostLink'){
			reactThis._showPostLink();
		}else if(action=='_editFeed'){
			reactThis._editFeed();
		}else if(typeof fn === 'function') {
			window[action](reactThis.props.feed);
		}else{
			window.location.href = action;
		}
	},
	render:function(){
		var reactThis = this;

		return(
			<div className="ui dropdown">
			  <i className="icon angle down icon"></i>
			  <div className="menu">
			  	{
			  		reactThis.state.actions.map(function(val, ind){
			  			return(	
			  				<a  onClick={reactThis._onAction.bind(reactThis, val.action, )} className="item">{val.label}</a>
			  			)
			  		})
			  	}
			  </div>
			</div>
		);
	}
});

var WallFeedEdit=React.createClass({
	_updateChange: function(event){
		var reactThis = this;
	    const target = event.target;
	    reactThis.props.update(target.innerHTML, reactThis.props.index, true);
	},
	componentDidMount:function(){
		var reactThis = this;
		reactThis.setState({content: reactThis.props.feed});
		var content = ReactDOM.findDOMNode(reactThis.refs['content']);
		content.setAttribute('contenteditable','');
		content.focus();
	},
	render:function(){
		var reactThis = this;
		return(
			<div>
				<div ref="content" className="feed-edit" name="content" onBlur={reactThis._updateChange} dangerouslySetInnerHTML={{__html: reactThis.props.feed}}></div>
			</div>
		);
	}
});

var WallFeeds=React.createClass({
	getInitialState: function() {
		return { 
	  		dataFeeds: [],
	  		showComment: {},
	  		isLoadMore: false,
	  		isLoading: [],
	  		isEditing: [],
	  		newPost: 0,
	  		more: [],
	  		page: 2,
	  		actions: [
		  		{default: 'like', label: 'Like', icon: 'like', class: 'like', action: "_like"},
		  		{default: 'comments', label: 'Comments', icon: 'comments', class: 'comments', action: "_comments"},

	  		],
		};
	},
	_getWallFeeds: function(){
		var reactThis = this;
		reactThis.props.read('shift_wall', '', reactThis, function(resp){
			reactThis.setState({dataFeeds: resp});
		});
	},
	_toHTML: function(html, index){
		var reactThis = this;
		return {__html: html };
	},
	_showMore: function(feed, index){
		var reactThis = this;
		var more = reactThis.state.more;
		more[index] = !more[index];
		reactThis.setState(more);
	},
	_timeSince: function(time){
		var reactThis = this;
		var timeagoInstance = timeago();
		return timeagoInstance.format(time);
	},
	_showComments: function(index, feed_id){
		var reactThis = this;
		var feed = reactThis.state.dataFeeds;
		var findex = reactThis._in_array(feed_id, feed, 'id');
		feed[findex]['showComment'] = true;
		reactThis.setState({dataFeeds : feed});
	},
	_attachmentType: function(attachment){
		var type = '';

		if(attachment.type){
			type = attachment.type.split("/")[0];
		}else{
			return;
		}

		switch(type){
			case 'video':
				return (
					<video id="video-attachment" width="100%" controls >
						<source src={attachment.attachment_url} type="video/webm" />
					</video>
				);
				break;
			case 'image':
				return (
					<a className="sw-popup" href={attachment.attachment_url} >
						<img src={attachment.attachment_thumbnail} />
					</a>
				);
				break;
			case 'application':
				var innerType = '';
				if(attachment.type){
					innerType = attachment.type.split("/")[1];
				}
				switch(innerType){
					case 'zip':

						return (
							<a className="sw-popup" href={'?f=' + attachment.attachment_url} target="_blank">
								<i className="attachment-file huge archive outline icon"></i>
							</a>
						);
						break;
					case 'msword':

						return (
							<a className="sw-popup" href={'?f=' + attachment.attachment_url} target="_blank">
								<i className="attachment-file huge word outline icon"></i>
							</a>
						);
						break;
					case 'pdf':
						return (
							<a className="sw-popup" href={'?f=' + attachment.attachment_url} target="_blank">
								<i className="attachment-file huge file pdf outline icon"></i>
							</a>
						);
						break;
					default:
						return (
							<a className="sw-popup" href={'?f=' + attachment.attachment_url} target="_blank">
								<i className="attachment-file huge file icon"></i>
							</a>
						);
				}
				break;
			default:
				return (
					<a className="sw-popup" href={'?f=' + attachment.attachment_url} target="_blank">
						<i className="attachment-file huge file text outline icon"></i>
					</a>
				);
		}
	},
	_loadMore: function(){

		var reactThis = this;
		reactThis.setState({isLoadMore: true});
		reactThis.props.read('shift_wall', {page: reactThis.state.page}, reactThis, function(resp){
			if(resp.length > 0){
				reactThis.setState({page: (reactThis.state.page + 1)});
				reactThis.setState({isLoadMore: false});
	    		var contactinated = reactThis.state.dataFeeds.concat(resp);
	    		reactThis.setState({ dataFeeds : contactinated });
			}else{
				reactThis.setState({isLoadMore: 404});
			}

		});

	},
	//Loop with callback experimental
	_loop: function(items = [], callback, finalCallback){

		let counter = 0;
		
		var asyncFunction = (item, index, cb) => {
		  // setTimeout(() => {
		    callback(item, index);
		    cb();
		  // }, 100);
		}
		
		let requests = items.reduce((promiseChain, item) => {

		    return promiseChain.then(() => new Promise((resolve) => {
		    	asyncFunction(item, counter, resolve);
		    	counter++
		    }));

		}, Promise.resolve());

		requests.then(() => {
			finalCallback();	
		});

	},
	_in_array: function(needle, haystack, key=''){

	    for (var i=0, len=haystack.length;i<len;i++) {
	    	if( key != '' ){
	    		if (haystack[i][key] == needle) return i;
	    	}else if (haystack[i] == needle) return i;
	    }
	    return false;
	},
	_likePost: function(fid, e){
		var reactThis = this;

		reactThis.props.update('shift_wall/'+fid, {
			actions: 'feed_like',
			_shift_wall_feed_liked: shiftwall.uid
		}, reactThis, function(resp){
			console.log(resp)
		});

	},
	_deleting: function(states, index){
		var reactThis = this;
		reactThis._showLoading(index);
	},
	_editing: function(feed, index, flag=false){
		var reactThis = this;
		if(flag){
			var dataFeeds = reactThis.state.dataFeeds;
			dataFeeds[index].content.rendered = feed;
			var isEditing = reactThis.state.isEditing;
			isEditing[index] = false;
			reactThis.setState({dataFeeds: dataFeeds, isEditing: isEditing});
			reactThis._showLoading(index);
			reactThis.props.update(
				'shift_wall/' + dataFeeds[index].id,
				{ actions: 'post_edit', 'content': feed },
				reactThis,
				function(resp){
					console.log('Update Post!');
					reactThis._showLoading(index);
				}
			);
		}else{
			var editing = {isEditing: reactThis.state.isEditing}
			editing.isEditing[index] = !editing.isEditing[index];	
	 		reactThis.setState(editing);
		}
	},
	_showLoading: function(index){
		var reactThis = this;
		var loading = {isLoading: reactThis.state.isLoading}
		loading.isLoading[index] = !loading.isLoading[index];	
 		reactThis.setState(loading);
	},
	_onAction: function(action, evnt){
		var reactThis = this;
		var fn = window[action];
		if(action=='_takePhoto'){
			reactThis._takePhoto();
		}else if(action=='_takeVideo'){
			reactThis._takeVideo();
		}else if(action=='_addAttachment'){
			reactThis._addAttachment();
		}else if(typeof fn === 'function') {
			window[action](reactThis.props.feed);
		}else{
			// window.location.href = action;
		}
	},
	componentDidMount:function(){
		var reactThis = this;
		reactThis._getWallFeeds();

		if (typeof shiftRT.on !== "function")
			return;

		//New feed
		shiftRT.on('shiftwall_create_feed', function(resp){
				console.log('shiftwall_create_feed', resp)
			if(resp.post_author != shiftwall.uid){
				var newPost = reactThis.state.newPost;
				newPost = newPost + 1;
				reactThis.setState({newPost: newPost});
			}
		});

		//Update feed
		shiftRT.on('shiftwall_update_feed', function(resp){
			var data = resp;
			var feed = reactThis.state.dataFeeds;
			var findex = reactThis._in_array(data.id, feed, 'id');
			feed[findex] = data;
			reactThis.setState({dataFeeds : feed});
		});

		// Delete feed
		shiftRT.on('shiftwall_delete_feed', function(resp){
			var data = resp;
			var feed = reactThis.state.dataFeeds;
			var findex = reactThis._in_array(data.id, feed, 'id');
			feed.splice(findex, 1);
			reactThis.setState({dataFeeds : feed});
		});

	},
	componentDidUpdate: function(){
		var reactThis = this;

		//Push new post update
		if(reactThis.props.componentBridge !== ''){
			var feed = reactThis.state.dataFeeds;
			feed.unshift(reactThis.props.componentBridge);
			reactThis.setState({dataFeeds : feed, newPost: 0});
			reactThis.props._componentBridge('');
		}

	},
	render:function(){
		var reactThis = this;
		

		console.log('Feeds', reactThis.state.dataFeeds)


		var loader = function(){
			return(
					<div className="ui active inverted dimmer">
				    	<div className="ui loader"></div>
				  	</div>
			);
		}

		//Need further improvements
		var _attachment = function(attchment, pIndex){

			var attachments = Object.keys(attchment).map(function(k) { 
				var items = [];
				var type = 'image';

				var group = attchment[k].map(function(val, ind){

					return reactThis._attachmentType({
						type: val.type,
						attachment_thumbnail: val.attachment_thumbnail,
						attachment_url: val.attachment_url
					});

				});	

				attchment[k].map(function(val, ind){

					var data_src = val.attachment_url;

					if(k != 'image'){
						type = 'inline';
						data_src = "<a href='#'><i class='attachment-file massive file text outline icon' ></i></h1>";
					}
					
					items.push({
						type: type,
						src: data_src
					})

				});

				setTimeout(function(){
					if(k == 'image'){
						jQuery("#group-" + k + pIndex).magnificPopup({
							delegate: 'a',
							gallery: { enabled: true },
							type: 'image' // this is default type
		    			});
					}

				}, 1000)

				return (
					<span id={"group-" + k + pIndex} className={"group-" + k}>
						{group}
					</span>
				)

			});	

			return(
				<div>
			  		{attachments}
			 	</div>
			);
				
		}

		// Post like & unlike
		var _isLiked = function(feed){
			return(
				<a className={"like " + 
				(reactThis._in_array(shiftwall.uid, feed._shift_wall_feed_liked) !== false ? 'active' : '') } onClick={reactThis._likePost.bind(reactThis, feed.id)}>
				  <i className="like icon"></i> {feed._shift_wall_feed_liked.length} Like
				</a>
			);
		}

		var _comments = function(feed, index){
			return(
				<a className="comments" onClick={reactThis._showComments.bind(reactThis, index, feed.id)}>
				  <i className="comments icon"></i> {feed.comment_count.all} Comments
				</a>
			);
		}			

		var showMore = function(feed, index){
			//Show more & less
			if(feed.length > shiftwall.settings.characterLength && reactThis.state.more[index]){
				return(<a onClick={reactThis._showMore.bind(reactThis, feed, index)}>less...</a>);
			}else if(feed.length > shiftwall.settings.characterLength && !reactThis.state.more[index]){
				return(<a onClick={reactThis._showMore.bind(reactThis, feed, index)}>more...</a>);
			}
		}

		var contents = function(feed, index){

   			if(!reactThis.state.isEditing[index]){

				if(feed.length > shiftwall.settings.characterLength && !reactThis.state.more[index]) {
	  				return(
	  					<div className="long-text">
	  						<div className="extra text" dangerouslySetInnerHTML={reactThis._toHTML(feed.substr(0, shiftwall.settings.characterLength) + '...', index, reactThis)} ></div>
	  						{showMore(feed, index)}
	  					</div>
	  				);
				}else{
	  				return(
	  					<div>
	  						<div className="extra text" dangerouslySetInnerHTML={reactThis._toHTML(feed, index, reactThis)} ></div>
	  						{showMore(feed, index)}
	  					</div>
	  				);
				}

  			}else{

  				return(
  					<WallFeedEdit 
  						feed={feed}
  						index={index}
  						update={reactThis._editing} />
  				);	
  			}
		}

		var feedActions = function(feed, index){

			return reactThis.state.actions.map(function(option, i){
				if(typeof option.default != 'undefined' && option.default === 'like'){
					return(_isLiked(feed));
				}else if(typeof option.default != 'undefined' && option.default === 'comments'){
					return(_comments(feed, index));
				}else{
					return(
						<a className={option.class} onClick={reactThis._onAction.bind(reactThis, option.action, )}>
			        		<i className={"icon " + option.icon}></i> {option.label}
			  			</a>
					);
				}
			}.bind(this));

		}

		var feeds = reactThis.state.dataFeeds.map(function(feed, index){

			return(
				<div id={"feed-" + feed.id} ref={"feed-" + feed.id} className="ui feed" key={index.toString()}>
			  		
			  		{ reactThis.state.isLoading[index] ? loader() : '' }

			  		<div className="event">

			  			<WallFeedActions 
				  			feed={feed}
				  			index={index}
				  			editing={reactThis._editing}
				  			delete={reactThis.props.delete}
				  			deleting={reactThis._deleting} />

					    <div className="label">
					      <img src={feed.author_meta.avatar} />
					    </div>
					    <div className="content">
							<div className="summary">
								<a className="author">{feed.author_meta.first_name} {feed.author_meta.last_name}</a>
								<div className="date">{this._timeSince(feed.date)}</div>
							</div>

				      		{ contents(feed.content.rendered, index) }

				      		<div className="attachment-group extra images small">
					      		{
					      			_attachment(feed._shift_wall_attachment, index)
	      						}
				      		</div>
							<div className="meta">
								
								{feedActions(feed, index)}

							</div>
					    </div>
				  	</div>
						{
							(typeof this.state.dataFeeds[index].showComment != 'undefined' && this.state.dataFeeds[index].showComment == true) ? <WallCommentFeeds feed={feed} create={this.props.create} read={this.props.read} update={this.props.update} delete={this.props.delete} /> : ''
						}
			  	</div>
		  	)

		}.bind(this));

		var newPost = function(){

			if( reactThis.state.newPost > 0 ){

				return(
					<div className="ui ignored info message text-center feed-new-post">
						You have {reactThis.state.newPost} new post.
					</div>
				);

			}

		}

		return(
			<div>

				{newPost()}

		  		{feeds}

		  		<button type="button" id="sw-load-more" className={"sw-load-more " + (reactThis.state.isLoadMore==404 ? 'hide': '' ) + " " + (reactThis.state.isLoadMore ? "loading" : "") +" fluid ui primary button"} onClick={reactThis._loadMore}>Load More</button>
		 	</div>
		);

	}
});

var WallCommentFeeds=React.createClass({
	getInitialState: function() {
		return { 
	  		dataComments: [],
	  		isLoading: true,
		};
	},
	_getFeedComments: function(){
		var reactThis = this;
		reactThis.props.read('comments', {post: reactThis.props.feed.id}, reactThis, function(resp){

			reactThis.setState({'dataComments': resp, isLoading: false});

		});
	},
	_toHTML: function(html, index){
		return {__html: html};
	},
	_in_array: function(needle, haystack, key=''){

	    for (var i=0, len=haystack.length;i<len;i++) {
	    	if( key != '' ){
	    		if (haystack[i][key] == needle) return i;
	    	}else if (haystack[i] == needle) return i;
	    }
	    return false;
	},
	_timeSince: function(time){
		var reactThis = this;
		var timeagoInstance = timeago();
		return timeagoInstance.format(time);
	},
	_deleteComment: function(comment, evnt){
		evnt.preventDefault();
		var reactThis = this;
		reactThis.setState({isLoading: true});
		reactThis.props.delete(
			'comments/' + comment.id,
			{actions: 'delete_comment'},
			reactThis,
			function(resp){
				reactThis.setState({isLoading: false});
			}
		);
	},
	_likeComment: function(cid, e){
		var reactThis = this;
		reactThis.props.update('comments/'+cid, {
			actions: 'comment_like',
			_shift_wall_comment_liked: shiftwall.uid
		}, reactThis, function(resp){
			console.log(resp)
		});
	},
	componentDidMount:function(){
		var reactThis = this;
		this._getFeedComments();

		if (typeof shiftRT.on !== "function")
			return;

		// New comment
		shiftRT.on('shiftwall_create_comment', function(resp){
			console.log('shiftwall_create_comment', resp);
			var comment = reactThis.state.dataComments;
			comment.unshift(resp);		
			reactThis.setState({dataComments : comment});
		});

		// Update comment
		shiftRT.on('shiftwall_update_comment', function(resp){
			console.log('shiftwall_update_comment', resp);
			var comment = reactThis.state.dataComments;
			var findex = reactThis._in_array(resp.id, comment, 'id');
			comment[findex] = resp;
			reactThis.setState({dataComments : comment});
		});

		// Delete comment
		shiftRT.on('shiftwall_delete_comment', function(resp){
			console.log('shiftwall_delete_comment', resp);
			var comment = reactThis.state.dataComments;
			var findex = reactThis._in_array(resp.id, comment, 'id');
			comment.splice(findex, 1);
			reactThis.setState({dataComments : comment});
		});

	},
	render: function(){
		var reactThis = this;

		var loader = function(){
			return(
					<div className="ui active inverted dimmer">
				    	<div className="ui loader"></div>
				  	</div>
			);
		}

		var deleteComment = function(comment){

			if(shiftwall.uid==reactThis.props.feed.author || shiftwall.uid==comment.author){
				return (<a className="commentDelete" href="#" onClick={reactThis._deleteComment.bind(this, comment)}>&times;</a>);
			}

		}

		// Post like & unlike
		var _isLiked = function(comment){

			return(
				<a className={"like " + (reactThis._in_array(shiftwall.uid, comment._shift_wall_comment_liked) !== false ? 'active' : '') } onClick={reactThis._likeComment.bind(reactThis, comment.id)}>
				<i className={"icon like " + (reactThis._in_array(shiftwall.uid, comment._shift_wall_comment_liked) !== false ? 'red' : '')}></i> {comment._shift_wall_comment_liked.length} Like</a>
			);
			
		}

		var comments = this.state.dataComments.map(function(comment,index){
	
			return(
		      	<div className="ui comments">

			    	<div className="comment">

						<a className="avatar">
							<img src={comment.author_avatar_urls[48]} />
						</a>

			          	<div className="content">
				          	
				          	{ deleteComment(comment) }
				            
				            <a className="author">{comment.author_name}</a>
				            <div className="metadata">
				              <span className="date">{this._timeSince(comment.date)}</span>
				            </div>
				            <div className="text" dangerouslySetInnerHTML={this._toHTML(comment.content.rendered, this)} >
				            </div>
				            <div className="actions">
				              { _isLiked(comment) }
				              <a className="reply"><i className="icon reply"></i> Reply</a>
				            </div>
				          </div>
			        </div>
		        </div>
			)
		}.bind(this));

		return(
			<div>
				<div id="commnetsFeed">
			  		<WallCommentForm fid={this.props.feed.id} create={this.props.create}/>
			  		{this.state.isLoading ? loader() : "" }
			  		{comments}
			 	</div>
		 	</div>
		);

	}
});

var WallContainer=React.createClass({
	getInitialState: function() {
		return { 
			shift: '',
	  		componentBridgeData: ''
		};
	},
	timeago: function(date) {

		var timeagoInstance = timeago();
		var nodes = document.querySelectorAll('.timeago');
		// use render method to render nodes in real time
		timeagoInstance.render(nodes);
	},
	_GET: function(url='shift_wall', encodedata, reactThis, success){

		jQuery.ajax({
			type:"GET",
			url:shiftwall.root + 'wp/v2/'+ url,
			data :encodedata,
			dataType:"json",
			cache:false,
			timeout:50000,
			beforeSend :function( xhr ) { 
				xhr.setRequestHeader( 'X-WP-Nonce', shiftwall.nonce );
			}.bind(this),
			success:function(data){
				success.call(this, data);
			}.bind(this),
			error:function(data){
				alert(data.responseJSON.message)
			}.bind(this)
		});

	},
	_PUT: function(url='shift_wall', encodedata, reactThis, success, attachtment = false){

		jQuery.ajax({
			type:"PUT",
			url:shiftwall.root + 'wp/v2/'+ url,
			data :encodedata,
			dataType:"json",
			cache:false,
			timeout:50000,
			beforeSend :function( xhr ) { 
				xhr.setRequestHeader( 'X-WP-Nonce', shiftwall.nonce );
			}.bind(this),
			success:function(data){
				success.call(this, data);
			}.bind(this),
			error:function(data){
				alert(data.responseJSON.message)
			}.bind(this)
		});

	
	},
	_POST: function(url='shift_wall', encodedata, reactThis, success, attachtment = false){

		if(attachtment){

			jQuery.ajax({
				type:"POST",
				url:shiftwall.root + 'wp/v2/'+ url,
				data :encodedata,
				cache:false,
				timeout:50000,
				contentType: false,
			    processData: false,
				beforeSend :function( xhr ) { 
					xhr.setRequestHeader( 'X-WP-Nonce', shiftwall.nonce );
				}.bind(this),
				success:function(data){
					success.call(this, data);
				}.bind(this),
				error:function(data){
					alert(data.responseJSON.message)
				}.bind(this)
			});

		}else{

			jQuery.ajax({
				type:"POST",
				url:shiftwall.root + 'wp/v2/'+ url,
				data :encodedata,
				dataType:"json",
				cache:false,
				timeout:50000,
				beforeSend :function( xhr ) { 
					xhr.setRequestHeader( 'X-WP-Nonce', shiftwall.nonce );
				}.bind(this),
				success:function(data){
					success.call(this, data);
				}.bind(this),
				error:function(data){
					alert(data.responseJSON.message)
				}.bind(this)
			});

		}

	},
	_DELETE: function(url='shift_wall', encodedata, reactThis, success){
		
		jQuery.ajax({
			type:"DELETE",
			data :encodedata,
			url:shiftwall.root + 'wp/v2/'+ url,
			beforeSend :function( xhr ) { 
				xhr.setRequestHeader( 'X-WP-Nonce', shiftwall.nonce );
			}.bind(this),
			success:function(data){
				success.call(this, data);
			}.bind(this),
			error:function(data){
				alert(data.responseJSON.message)
			}.bind(this)
		});

	},
	_componentBridge( val ){
		var reactThis = this;
		reactThis.setState({ componentBridgeData: val });
	},
	componentWillMount(){
		var reactThis = this;

		// console.log(new shiftWall())
	},
   	render:function(){
   		var reactThis = this;

	    return(
			<div id="wallContainer">
		    	<h1>Shift Wall</h1>
		    	<WallPostForm 
		    		setFeedPost={reactThis._POST}

		    		_componentBridge={reactThis._componentBridge}
		    		componentBridge={reactThis.state.componentBridgeData} />
				<div className="ui divider"></div>
				<WallFeeds 
					create={reactThis._POST}
					read={reactThis._GET}
					update={reactThis._PUT}
					delete={reactThis._DELETE}
					timeSince={reactThis.timeago}

					_componentBridge={reactThis._componentBridge}
					componentBridge={reactThis.state.componentBridgeData} />
		    </div>
	    );
   }
});

ReactDOM.render(
  <WallContainer />,
  document.getElementById('shiftwall-container')
);



