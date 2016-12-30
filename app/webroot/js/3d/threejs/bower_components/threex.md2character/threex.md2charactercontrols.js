document.addEventListener("contextmenu", function(e){
    e.preventDefault();
}, false);

var THREEx	= THREEx || {}


THREEx.MD2CharacterControls	= function(object3d, inputs){
	// update function
	var onRenderFcts= [];
	this.update	= function(delta, now){
		onRenderFcts.forEach(function(onRenderFct){
			onRenderFct(delta, now)
		})
	}
	// exports the inputs
	this.inputs	= inputs	|| {
		right	: false,
		left	: false,
		up	: false,
		down	: false,
	}
	inputs		= this.inputs

	// parameters
	this.angularSpeed	= Math.PI*2*0.5/2
	//this.linearSpeed	= 2.5
	this.linearSpeed = playerSpeed;
	
	onRenderFcts.push(function(delta, now){
		
		if (inputs.shift)
			this.linearSpeed = playerSpeed/2
		else
			this.linearSpeed = playerSpeed;
		
		if( inputs.right)	object3d.rotation.y	-= this.angularSpeed*delta
		if( inputs.left)	object3d.rotation.y	+= this.angularSpeed*delta
		if (Math.abs(mouseX - window.innerWidth/2) > window.innerWidth/2/2) object3d.rotation.y += delta*this.angularSpeed*(window.innerWidth/2 - mouseX)/window.innerWidth/2*5

		// up/down
		var distanceZ	= 0;
		var distanceX = 0;
		if( inputs.up)			distanceZ	= +this.linearSpeed * delta;
		if( inputs.down)		distanceZ	= -this.linearSpeed * delta;
		if( inputs.moveleft)	distanceX	= +this.linearSpeed * delta;
		if( inputs.moveright)	distanceX	= -this.linearSpeed * delta;
		if (distanceX && distanceZ) {
			distanceX /= 1.414213562
			distanceZ /= 1.414213562
		}

		if( distanceZ || distanceX ){
			var velocity = new THREE.Vector3(distanceX,0,distanceZ);
			var matrix	= new THREE.Matrix4().makeRotationY(object3d.rotation.y);
			velocity.applyMatrix4( matrix );
			object3d.position.add(velocity);
			if (!walls)
				return;
			var wallThickness = 0.4
			if (object3d.position.x > cubeSize*mapScale*worldWidth/2 - wallThickness -mapScale/2*cubeSize)
				object3d.position.x = cubeSize*mapScale*worldWidth/2 - wallThickness -mapScale/2*cubeSize
				
			if (object3d.position.x < -cubeSize*mapScale*worldWidth/2 + wallThickness -mapScale/2*cubeSize)
				object3d.position.x = -cubeSize*mapScale*worldWidth/2 + wallThickness -mapScale/2*cubeSize
				
			if (object3d.position.z > cubeSize*mapScale*worldDepth/2 - wallThickness -mapScale/2*cubeSize)
				object3d.position.z = cubeSize*mapScale*worldDepth/2 - wallThickness -mapScale/2*cubeSize
			
			if (object3d.position.z < -cubeSize*mapScale*worldDepth/2 + wallThickness -mapScale/2*cubeSize)
				object3d.position.z = -cubeSize*mapScale*worldDepth/2 + wallThickness -mapScale/2*cubeSize
		}
	}.bind(this))
}