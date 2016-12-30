<!DOCTYPE html>
<html lang = "fr">
<head>
	<script>
		var root_folder =  '<?php echo(ROOT_FOLDER).'js/3d/'; ?>' 
		var playerSpeed = <?php echo $playerSpeed; ?>;
	</script>
<?php
	echo $this->Html->css('https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css');
	echo $this->Html->css('bootstrap.min');
	echo $this->Html->css('game');
	
	echo $this->Html->script('3d/threejs/vendor/three.min.js');
	echo $this->Html->script('3d/threejs/vendor/imageUtils.js');
	echo $this->Html->script('3d/threejs/vendor/morphAnimMesh.js');
	
	echo $this->Html->script('3d/threejs/bower_components/threex.md2character/threex.md2character.js');
	echo $this->Html->script('3d/threejs/bower_components/threex.md2character/threex.md2characterratmahatta.js');
	echo $this->Html->script('3d/threejs/bower_components/threex.md2character/threex.md2charactercontrols.js');
	
	echo $this->Html->script('3d/threejs/js/controls/FirstPersonControls.js');
	echo $this->Html->script('3d/threejs/js/ImprovedNoise.js');
	echo $this->Html->script('3d/threejs/js/Detector.js');
	echo $this->Html->script('3d/threejs/js/libs/stats.min.js');

	echo $this->Html->script('3d/threejs/js/shaders/SkyShader.js');
	echo $this->Html->script('3d/threejs/js/shaders/CopyShader.js');
	echo $this->Html->script('3d/threejs/js/shaders/BokehShader.js');
	
	echo $this->Html->script('3d/threejs/js/postprocessing/EffectComposer.js');
	echo $this->Html->script('3d/threejs/js/postprocessing/RenderPass.js');
	echo $this->Html->script('3d/threejs/js/postprocessing/ShaderPass.js');
	echo $this->Html->script('3d/threejs/js/postprocessing/MaskPass.js');
	echo $this->Html->script('3d/threejs/js/postprocessing/BokehPass.js');
	
	echo $this->Html->script('3d/threejs/js/controls/TrackballControls.js');
	echo $this->Html->script('3d/threejs/js/GPUParticleSystem.js');
	
	echo $this->Html->script('3d/threejs/js/jquery-1.11.1.min.js');
?>

<title>Monolithic :: The Game</title>

</head>


<body>
	<div id="badass-captions">
		<span>KILL!</span>
		<span></span>
		<span></span>
	</div>
	<div id="userinfo">
		<div id="life-indicator">
			<i class="fa fa-heartbeat" style="font-size: 1.5em"></i>
			<span id="lifepercentage"> 100%</span>
		</div>
		<span id="xp-title">Niveau <?php echo $level; ?> : <?php echo (int)(100*$xp/$level) ?>%</span>
		<div class="barredxp">
			<div class="barre">
				<div id="xp" style="width:<?php echo (int)(100*$xp/$level) ?>%;"></div>
			</div>
		</div>
	</div>
	<a id="retour-vestiaire" href="http://<?php echo $_SERVER['HTTP_HOST'].substr(ROOT_FOLDER,0,-1).EDITFIGHTER; ?>"><i class="fa fa-arrow-circle-o-left"></i> Vestiaire</a>

	<div id="blood-overlay">
		<span id="killer-name">GAME OVER</span> t'a tué!
		<p>
			<a href="http://<?php echo $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>" class="btn btn-primary"  >Revenir</a>
			<a href="http://<?php echo $_SERVER['HTTP_HOST'].substr(ROOT_FOLDER,0,-1).EDITFIGHTER; ?>" class="btn btn-secondary">Vestiaire</a>
		</p>
	</div>
	<div id="container"><br /><br /><br /><br /><br />Reste Tranquille...</div>
	<script>
	
		
	//////////////////////////////////////////////////////////////////////////////////
	//		RENDERER, SCENE & CAMERA   												//
	//////////////////////////////////////////////////////////////////////////////////
	var renderer	= new THREE.WebGLRenderer({
		antialias	: true
	});
	renderer.setSize( window.innerWidth, window.innerHeight );
	renderer.setClearColor( 0xffffff );
	renderer.setPixelRatio( window.devicePixelRatio );
	
	var onRenderFcts= [];
	var scene	= new THREE.Scene();
	camera = new THREE.PerspectiveCamera( 60, window.innerWidth / window.innerHeight, 0.1, 800000 );
	
	

	//////////////////////////////////////////////////////////////////////////////////
	//		CANVAS					   												//
	//////////////////////////////////////////////////////////////////////////////////
	container = document.getElementById( 'container' );
	container.innerHTML = "";
	container.appendChild( renderer.domElement );

	stats = new Stats();
	stats.domElement.style.position = 'absolute';
	stats.domElement.style.top = '0px';
	stats.domElement.style.right = '0px';
	container.appendChild( stats.domElement );
	window.addEventListener( 'resize', onWindowResize, false );
	
	
	
	
	//////////////////////////////////////////////////////////////////////////////////
	//		CIEL					   												//
	//////////////////////////////////////////////////////////////////////////////////
	var effectController;
	(function () {
		// Ciel
		sky = new THREE.Sky();
		sky.mesh.rotation.y = Math.PI;
		scene.add( sky.mesh );
		
		// Soleil
		sunSphere = new THREE.Mesh(
			new THREE.SphereBufferGeometry( 20000, 16, 8 ),
			new THREE.MeshBasicMaterial( { color: 0xffffff } )
		);
		sunSphere.position.y = - 700000;
		sunSphere.visible = false;
		sunSphere.rotation.y = Math.PI;
		scene.add( sunSphere );
		
		// Paramètres de base du ciel
		effectController  = {
			turbidity: 10,
			reileigh: 2.3,
			mieCoefficient: 0.02,
			mieDirectionalG: 0.8,
			luminance: 0.1,
			inclination: 0.5, // elevation / inclination
			azimuth: 0.25, // Facing front,
			sun: ! true
		};
		
		
		// Jour et Nuit
		var oldinclination;
		var nuit = true;
		var joureffectue = true;
		setInterval(function(){
			oldinclination = effectController.inclination;
			var sunspeed = 30000;
			effectController.inclination = 0.2565 +0.2565*Math.sin((Date.now()%300000)/sunspeed)
			
			if (effectController.inclination - oldinclination < 0 && joureffectue) {
				nuit = !nuit;
				joureffectue = false
			} else if (effectController.inclination - oldinclination >= 0) {
				joureffectue = true
			}
			
			if (nuit) {
				// NUIT
				scene.fog = new THREE.FogExp2( 0x0C0C0C, 0.05 );
				effectController.turbidity = 1
				effectController.reileigh = 0
				effectController.luminance = 1
				if (ambientLight && directionalLight) {
						ambientLight.color = new THREE.Color(0.05,0.05,0.05)
						directionalLight.color = new THREE.Color(0.05,0.05,0.05)
				}
			} else {
				// JOUR
				scene.fog = new THREE.FogExp2( 0xffffff, 0.05 );
				effectController.turbidity = 10
				effectController.reileigh = 2.3
				effectController.luminance = 0.1
				if (ambientLight && directionalLight) {
					if (effectController.inclination < 0.49) {
						scene.fog = new THREE.FogExp2( 0x909090, 0.05 );
						ambientLight.color = new THREE.Color(0.27,0.33,0.27)
						directionalLight.color = new THREE.Color(0.54,0.60,0.54)
					} else {
						scene.fog = new THREE.FogExp2( 0x454545, 0.05 );
						ambientLight.color = new THREE.Color(0.16,0.19,0.16)
						directionalLight.color = new THREE.Color(0.30,0.35,0.30)
					}
				}
			}
			
			// Application des paramètres
			var distance = 400000;
			var uniforms = sky.uniforms;
			uniforms.turbidity.value = effectController.turbidity;
			uniforms.reileigh.value = effectController.reileigh;
			uniforms.luminance.value = effectController.luminance;
			uniforms.mieCoefficient.value = effectController.mieCoefficient;
			uniforms.mieDirectionalG.value = effectController.mieDirectionalG;
			
			var theta = Math.PI * ( effectController.inclination - 0.5 );
			var phi = 2 * Math.PI * ( effectController.azimuth - 0.5 );
			sunSphere.position.x = distance * Math.cos( phi );
			sunSphere.position.y = distance * Math.sin( phi ) * Math.sin( theta );
			sunSphere.position.z = distance * Math.sin( phi ) * Math.cos( theta );
			sunSphere.visible = effectController.sun;
			sky.uniforms.sunPosition.value.copy( sunSphere.position);
		}, 50);
	})()
	
	
	
	//////////////////////////////////////////////////////////////////////////////////
	//		CRÉATION DE LA CARTE DE JEU   											//
	//////////////////////////////////////////////////////////////////////////////////
	var fogExp2 = true;
	var container, stats;
	var controls;
	var mesh, mat;
	//var worldWidth = 150, worldDepth = 150;
	var worldWidth = 75, worldDepth = 75;
	var cubeSize = 100;
	var mapScale = 0.0033;
	var worldHalfWidth = worldWidth / 2, worldHalfDepth = worldDepth / 2;
	var data = generateHeight( worldWidth, worldDepth );
	var clock = new THREE.Clock();
	var ambientLight, directionalLight;
	var walls;

	(function() {
		// Brouillard
		scene.fog = new THREE.FogExp2( 0x403030, 0.03 );

		// Création d'un cube, avec les 6 côtés
		var light = new THREE.Color( 0xffffff );
		var shadow = new THREE.Color( 0x505050 );
		var matrix = new THREE.Matrix4();

		var pxGeometry = new THREE.PlaneGeometry( cubeSize, cubeSize );
		pxGeometry.faces[ 0 ].vertexColors = [ light, shadow, light ];
		pxGeometry.faces[ 1 ].vertexColors = [ shadow, shadow, light ];
		pxGeometry.faceVertexUvs[ 0 ][ 0 ][ 0 ].y = 0.5;
		pxGeometry.faceVertexUvs[ 0 ][ 0 ][ 2 ].y = 0.5;
		pxGeometry.faceVertexUvs[ 0 ][ 1 ][ 2 ].y = 0.5;
		pxGeometry.rotateY( Math.PI / 2 );
		pxGeometry.translate( cubeSize/2, 0, 0 );

		var nxGeometry = new THREE.PlaneGeometry( cubeSize, cubeSize );
		nxGeometry.faces[ 0 ].vertexColors = [ light, shadow, light ];
		nxGeometry.faces[ 1 ].vertexColors = [ shadow, shadow, light ];
		nxGeometry.faceVertexUvs[ 0 ][ 0 ][ 0 ].y = 0.5;
		nxGeometry.faceVertexUvs[ 0 ][ 0 ][ 2 ].y = 0.5;
		nxGeometry.faceVertexUvs[ 0 ][ 1 ][ 2 ].y = 0.5;
		nxGeometry.rotateY( - Math.PI / 2 );
		nxGeometry.translate( - cubeSize/2, 0, 0 );

		var pyGeometry = new THREE.PlaneGeometry( cubeSize, cubeSize );
		pyGeometry.faces[ 0 ].vertexColors = [ light, light, light ];
		pyGeometry.faces[ 1 ].vertexColors = [ light, light, light ];
		pyGeometry.faceVertexUvs[ 0 ][ 0 ][ 1 ].y = 0.5;
		pyGeometry.faceVertexUvs[ 0 ][ 1 ][ 0 ].y = 0.5;
		pyGeometry.faceVertexUvs[ 0 ][ 1 ][ 1 ].y = 0.5;
		pyGeometry.rotateX( - Math.PI / 2 );
		pyGeometry.translate( 0, cubeSize/2, 0 );

		var py2Geometry = new THREE.PlaneGeometry( cubeSize, cubeSize );
		py2Geometry.faces[ 0 ].vertexColors = [ light, light, light ];
		py2Geometry.faces[ 1 ].vertexColors = [ light, light, light ];
		py2Geometry.faceVertexUvs[ 0 ][ 0 ][ 1 ].y = 0.5;
		py2Geometry.faceVertexUvs[ 0 ][ 1 ][ 0 ].y = 0.5;
		py2Geometry.faceVertexUvs[ 0 ][ 1 ][ 1 ].y = 0.5;
		py2Geometry.rotateX( - Math.PI / 2 );
		py2Geometry.rotateY( Math.PI / 2 );
		py2Geometry.translate( 0, cubeSize/2, 0 );

		var pzGeometry = new THREE.PlaneGeometry( cubeSize, cubeSize );
		pzGeometry.faces[ 0 ].vertexColors = [ light, shadow, light ];
		pzGeometry.faces[ 1 ].vertexColors = [ shadow, shadow, light ];
		pzGeometry.faceVertexUvs[ 0 ][ 0 ][ 0 ].y = 0.5;
		pzGeometry.faceVertexUvs[ 0 ][ 0 ][ 2 ].y = 0.5;
		pzGeometry.faceVertexUvs[ 0 ][ 1 ][ 2 ].y = 0.5;
		pzGeometry.translate( 0, 0, cubeSize/2 );

		var nzGeometry = new THREE.PlaneGeometry( cubeSize, cubeSize );
		nzGeometry.faces[ 0 ].vertexColors = [ light, shadow, light ];
		nzGeometry.faces[ 1 ].vertexColors = [ shadow, shadow, light ];
		nzGeometry.faceVertexUvs[ 0 ][ 0 ][ 0 ].y = 0.5;
		nzGeometry.faceVertexUvs[ 0 ][ 0 ][ 2 ].y = 0.5;
		nzGeometry.faceVertexUvs[ 0 ][ 1 ][ 2 ].y = 0.5;
		nzGeometry.rotateY( Math.PI );
		nzGeometry.translate( 0, 0, - cubeSize/2 );



		// Génération de la carte en positionnant n cubes
		var geometry = new THREE.Geometry();
		var dummy = new THREE.Mesh();
		for ( var z = 0; z < worldDepth; z ++ ) {
			for ( var x = 0; x < worldWidth; x ++ ) {

				var h = getY( x, z );

				matrix.makeTranslation(
					x * cubeSize - worldHalfWidth * cubeSize,
					h * cubeSize,
					z * cubeSize - worldHalfDepth * cubeSize
				);

				var px = getY( x + 1, z );
				var nx = getY( x - 1, z );
				var pz = getY( x, z + 1 );
				var nz = getY( x, z - 1 );

				var pxpz = getY( x + 1, z + 1 );
				var nxpz = getY( x - 1, z + 1 );
				var pxnz = getY( x + 1, z - 1 );
				var nxnz = getY( x - 1, z - 1 );

				var a = nx > h || nz > h || nxnz > h ? 0 : 1;
				var b = nx > h || pz > h || nxpz > h ? 0 : 1;
				var c = px > h || pz > h || pxpz > h ? 0 : 1;
				var d = px > h || nz > h || pxnz > h ? 0 : 1;

				if ( a + c > b + d ) {

					var colors = py2Geometry.faces[ 0 ].vertexColors;
					colors[ 0 ] = b === 0 ? shadow : light;
					colors[ 1 ] = c === 0 ? shadow : light;
					colors[ 2 ] = a === 0 ? shadow : light;

					var colors = py2Geometry.faces[ 1 ].vertexColors;
					colors[ 0 ] = c === 0 ? shadow : light;
					colors[ 1 ] = d === 0 ? shadow : light;
					colors[ 2 ] = a === 0 ? shadow : light;
					
					geometry.merge( py2Geometry, matrix );

				} else {

					var colors = pyGeometry.faces[ 0 ].vertexColors;
					colors[ 0 ] = a === 0 ? shadow : light;
					colors[ 1 ] = b === 0 ? shadow : light;
					colors[ 2 ] = d === 0 ? shadow : light;

					var colors = pyGeometry.faces[ 1 ].vertexColors;
					colors[ 0 ] = b === 0 ? shadow : light;
					colors[ 1 ] = c === 0 ? shadow : light;
					colors[ 2 ] = d === 0 ? shadow : light;
					
					geometry.merge( pyGeometry, matrix );

				}

				if ( ( px != h && px != h + 1 ) || x == 0 ) {

					var colors = pxGeometry.faces[ 0 ].vertexColors;
					colors[ 0 ] = pxpz > px && x > 0 ? shadow : light;
					colors[ 2 ] = pxnz > px && x > 0 ? shadow : light;

					var colors = pxGeometry.faces[ 1 ].vertexColors;
					colors[ 2 ] = pxnz > px && x > 0 ? shadow : light;

					geometry.merge( pxGeometry, matrix );

				}

				if ( ( nx != h && nx != h + 1 ) || x == worldWidth - 1 ) {

					var colors = nxGeometry.faces[ 0 ].vertexColors;
					colors[ 0 ] = nxnz > nx && x < worldWidth - 1 ? shadow : light;
					colors[ 2 ] = nxpz > nx && x < worldWidth - 1 ? shadow : light;

					var colors = nxGeometry.faces[ 1 ].vertexColors;
					colors[ 2 ] = nxpz > nx && x < worldWidth - 1 ? shadow : light;

					geometry.merge( nxGeometry, matrix );

				}

				if ( ( pz != h && pz != h + 1 ) || z == worldDepth - 1 ) {

					var colors = pzGeometry.faces[ 0 ].vertexColors;
					colors[ 0 ] = nxpz > pz && z < worldDepth - 1 ? shadow : light;
					colors[ 2 ] = pxpz > pz && z < worldDepth - 1 ? shadow : light;

					var colors = pzGeometry.faces[ 1 ].vertexColors;
					colors[ 2 ] = pxpz > pz && z < worldDepth - 1 ? shadow : light;

					geometry.merge( pzGeometry, matrix );

				}

				if ( ( nz != h && nz != h + 1 ) || z == 0 ) {

					var colors = nzGeometry.faces[ 0 ].vertexColors;
					colors[ 0 ] = pxnz > nz && z > 0 ? shadow : light;
					colors[ 2 ] = nxnz > nz && z > 0 ? shadow : light;

					var colors = nzGeometry.faces[ 1 ].vertexColors;
					colors[ 2 ] = nxnz > nz && z > 0 ? shadow : light;

					geometry.merge( nzGeometry, matrix );

				}
			
			}
		}

		/* Texture & Matériau */
		var texture = THREE.ImageUtils.loadTexture( '<?php echo (ROOT_FOLDER.'js/3d/threejs/textures/minecraft/diamant.png'); ?>' );
		texture.magFilter = THREE.NearestFilter;
		texture.minFilter = THREE.LinearMipMapLinearFilter;
		var mesh = new THREE.Mesh( geometry, new THREE.MeshLambertMaterial( { map: texture, vertexColors: THREE.VertexColors } ) );
		
		/* Mise à l'échelle de la carte pour correspondre à la taille du joueur */
		mesh.scale.set(mapScale, mapScale, mapScale)
		scene.add( mesh );

		/* Éclairage ambiant */
		ambientLight = new THREE.AmbientLight( 0x445444 );
		scene.add( ambientLight );
		
		directionalLight = new THREE.DirectionalLight( 0x889888, 2 );
		directionalLight.position.set( 1, 1, 0.5 ).normalize();
		scene.add( directionalLight );
		
		var glassTexture = THREE.ImageUtils.loadTexture( '<?php echo (ROOT_FOLDER.'js/3d/threejs/textures/minecraft/verre2.png'); ?>' );
		var glassMaterial = new THREE.MeshBasicMaterial({ map: glassTexture, side: THREE.BackSide, transparent: true });
		walls = new THREE.Mesh( new THREE.CubeGeometry( cubeSize*mapScale*worldWidth, cubeSize*mapScale*(worldWidth+worldDepth)/2, cubeSize*mapScale*worldDepth ), glassMaterial );
		walls.position.y = cubeSize*mapScale*(worldWidth+worldDepth)/2/3.3*0.5;
		walls.position.x -=mapScale/2*cubeSize;
		walls.position.z -=mapScale/2*cubeSize;
		scene.add( walls );
		
	})();
	
	
	
	
	//////////////////////////////////////////////////////////////////////////////////
	//		ECLAIRAGE AUTOUR DU JOUEUR						                        //
	//////////////////////////////////////////////////////////////////////////////////
	;(function(){
		// add a ambient light
		var light	= new THREE.AmbientLight( 0x020202 )
		scene.add( light )
		// add a light in front
		var light	= new THREE.DirectionalLight('white', 0.6)
		light.position.set(0.5, 0.5, 2)
		scene.add( light )
		// add a light behind
		var light	= new THREE.DirectionalLight('white', 1)
		light.position.set(-0.5, -0.5, -2)
		scene.add( light )		
	})()
	

	
	//////////////////////////////////////////////////////////////////////////////////
	//		RATAMAHATTA : JOUEUR													//
	//////////////////////////////////////////////////////////////////////////////////
	var ratamahatta	= new THREEx.MD2CharacterRatmahatta()
	var health = <?php echo $health; ?>;
	var originalHealth = <?php echo $health; ?>;
	ratamahatta.character.object3d.position.x = (0.5 - Math.random())*cubeSize*worldWidth*mapScale*0.9;
	ratamahatta.character.object3d.position.z = (0.5 - Math.random())*cubeSize*worldDepth*mapScale*0.9;
	ratamahatta.character.object3d.position.y += getRealY(ratamahatta.character.object3d.position.x,ratamahatta.character.object3d.position.z)
	ratamahatta.character.object3d.rotation.y = Math.random()*2*Math.PI;
	scene.add(ratamahatta.character.object3d)
		
	onRenderFcts.push(function(delta){
		ratamahatta.update(delta)
	})
	ratamahatta.character.addEventListener('loaded', function(){
		ratamahatta.setSkinName('<?php echo $skin; ?>')
		ratamahatta.setWeaponName('<?php echo $weapon; ?>')
	})

	
	
	//////////////////////////////////////////////////////////////////////////////////
	//		RATAMAHATTA : AUTRES JOUEURS											//
	//////////////////////////////////////////////////////////////////////////////////
	var autresJoueurs = [];
	function ajouterJoueur(fighter) {
		var idFighter = fighter[0];
		var nameFighter = fighter[1];
		var ballSpeed = fighter[2];
		var playerSpeed = fighter[3];
		
		for (var i = 0; i < autresJoueurs.length; i++)
			if (autresJoueurs[i].id == idFighter) 
				return;

		autresJoueurs.push({id: idFighter})
		var j	= new THREEx.MD2CharacterRatmahatta()
		j.character.object3d.position.y = Math.random()*5
		
		j.character.addEventListener('loaded', function(){
			j.setSkinName(fighter[5])
			j.setWeaponName(fighter[4])
			j.setAnimationName( 'stand' )
			var i;
			for (i = 0; i < autresJoueurs.length; i++)
				if (autresJoueurs[i].id == idFighter) 
					break;
			autresJoueurs[i].mesh = j;
			
			var geometry = new THREE.SphereGeometry( 0.05, 32, 32 );
			var material = new THREE.MeshBasicMaterial( {color: 0xFF0000} );
			var balle = new THREE.Mesh( geometry, material );
			
			balle.position.x = 0;
			balle.position.y = -10000;
			balle.position.z = 0;
			
			autresJoueurs[i].balle = balle
			autresJoueurs[i].ballSpeed = ballSpeed
			autresJoueurs[i].playerSpeed = playerSpeed
			autresJoueurs[i].immuneUntil = Date.now() + 5000;
			scene.add(j.character.object3d)
			scene.add(balle)
		})
	}
	
	// push annimation des joueurs
	var deplacementBall = new THREE.Vector3(0, 0, 0 )
	onRenderFcts.push(function(delta){
		autresJoueurs.forEach(function(j) {
			if (j.mesh)
				j.mesh.update(delta)
			if (j.posy) {
				var aspeed	= Math.PI*2*0.5/2
				var lspeed	= j.playerSpeed
				var p = j.mesh.character.object3d;
				var deplacement = new THREE.Vector3( j.posx, j.posy, j.posz )
				
				// RESPAWN
				if (deplacement.length() < 0.1) {
					p.rotation.y = parseFloat(j.roty);
					p.position.x = parseFloat(j.posx)
					p.position.y = parseFloat(j.posy)
					p.position.z = parseFloat(j.posz)
					return;
				}
				// Rotation
				if (j.roty < p.rotation.y)	p.rotation.y	-= aspeed*delta
				if (j.roty > p.rotation.y)	p.rotation.y	+= aspeed*delta
				

				// Déplacement
				var pointa = p.position;
				deplacement.sub(pointa);
				var n = Math.sqrt(deplacement.x*deplacement.x + deplacement.y*deplacement.y + deplacement.z*deplacement.z)
				if (n >= 0.07 && n < 5) {
					deplacement.normalize();
					deplacement.multiplyScalar( delta*lspeed );
					p.position.add(deplacement)
				} else {
					p.rotation.y = parseFloat(j.roty);
					p.position.x = parseFloat(j.posx)
					p.position.y = parseFloat(j.posy)
					p.position.z = parseFloat(j.posz)
				}
			}
			if (j.balleposy && j.balle) {
				// Déplacement
				var pointa1 = j.balle.position;
				var dir = deplacementBall.clone()
				dir.normalize()
				
				if (pointa1.y < -9000 || j.balleposy < -9000) {
					j.balle.position.x = parseFloat(j.balleposx) 
					j.balle.position.y = parseFloat(j.balleposy)
					j.balle.position.z = parseFloat(j.balleposz)
					return;
				}

				pointa1 = j.balle.position.clone();
				
				deplacementBall.x = parseFloat(j.balleposx )
				deplacementBall.y = parseFloat(j.balleposy)
				deplacementBall.z = parseFloat(j.balleposz )
				deplacementBall.sub(pointa1);
				deplacementBall.normalize();
/*
				// check direction
				var prodScal = dir.dot(deplacementBall)
				var angle = Math.acos(prodScal)
				console.log(angle)
				if(angle && angle > 0.1) {
					j.balle.position.x = parseFloat(j.balleposx) 
					j.balle.position.y = parseFloat(j.balleposy)
					j.balle.position.z = parseFloat(j.balleposz)
					return;
				}
*/
				deplacementBall.multiplyScalar( delta*j.ballSpeed);
				j.balle.position.add(deplacementBall)
			}
		})
	})
	
	function retirerJoueur(idFighter) {
		for (var i = 0; i < autresJoueurs.length; i++) {
			var j = autresJoueurs[i];
			if (j.id != idFighter) continue;
			scene.remove(j.mesh.character.object3d);
			scene.remove(j.balle)
			delete j;
			autresJoueurs.splice(i, 1);
		}
	}
	
	
	//////////////////////////////////////////////////////////////////////////////////
	//		Ajout de particule	     					        					//
	//////////////////////////////////////////////////////////////////////////////////
	
	particleSystem = new THREE.GPUParticleSystem({
		  maxParticles: 250000
		});
	// options passed during each spawned
	var options = {
		position: new THREE.Vector3(0,0,0),
		positionRandomness: 0,
		velocity: new THREE.Vector3(),
		velocityRandomness: 0.05,
		color: 0xff0020,
		colorRandomness: 0.33,
		turbulence: 0,
		lifetime: 2,
		size: 2,
		sizeRandomness: 1
	}
	var spawnerOptions = {
        spawnRate: 10000,
        horizontalSpeed: 0.01,
        verticalSpeed: 0.01,
        timeScale: 1
    }
    var tick = 0;
    
	function particule(posX, posY, posZ) {
		
		var delta = clock.getDelta() * spawnerOptions.timeScale;
		tick += delta;
		if (tick < 0) tick = 0;

		if (delta > 0) {
		  options.position.x = posX;
		  options.position.y = posY;
		  options.position.z = posZ;
		
		  for (var x = 0; x < spawnerOptions.spawnRate * delta; x++) {
		    particleSystem.spawnParticle(options);
		  }
		}
		particleSystem.update(tick);
	}
	
	
	//////////////////////////////////////////////////////////////////////////////////
	//		TIRER																	//
	//////////////////////////////////////////////////////////////////////////////////
	var seuilToucher = 1;
	var rapiditeBalle = <?php echo $ballSpeed; ?>;
	var dureeVieBalle = 20000/rapiditeBalle
	var seuilRecul = 0.3;
	var puissanceRecul = 1/300;
	var balleAnticipeePos = new THREE.Vector3( 0, -10000, 0 )
	var audioTirer = new Audio(root_folder+'audio/gun_sound.mp3');
	audioTirer.volume = 0.07;
    var balles = [];
	function ajouterBalle() {
		if (Math.random() > seuilRecul)
			mouseY -= window.innerHeight/2*puissanceRecul
		if (balles.length > 0)
			return;

		var audioTirerClone = audioTirer.cloneNode();
		audioTirerClone.volume = 0.07;
		audioTirerClone.play();

	    var geometry = new THREE.SphereGeometry( 0.05, 32, 32 );
		var material = new THREE.MeshBasicMaterial( {color: 0x000000} );
		var balle = new THREE.Mesh( geometry, material );
		balles.push({balle: balle, expiration:Date.now()+dureeVieBalle+refreshTime, direction: new THREE.Vector3(0,0,0)});
		
		balle.position.x = ratamahatta.character.object3d.position.x;
		balle.position.y = ratamahatta.character.object3d.position.y;
		balle.position.z = ratamahatta.character.object3d.position.z;
		
		var mat = new THREE.Matrix4();
		mat.makeRotationY(ratamahatta.character.object3d.rotation.y)
		var offsetBras = new THREE.Vector3(0.2,0.7,-0.2)
		offsetBras.applyMatrix4( mat )
		balle.position.add(offsetBras)
		
		
        var pp = projectionSouris(mouseX, mouseY)
        if (!pp)
        	return;
        var direction = pp.sub(camera.position).normalize()
		
		scene.add( balle );
		balleAnticipeePos = balle.position.clone()
		balles[balles.length-1] = {balle: balle, expiration:Date.now()+dureeVieBalle+refreshTime, direction: direction};
		
	}
	
	function projectionSouris(cX, cY){
            var planeZ = new THREE.Plane(new THREE.Vector3(0, 0, 1), 0);
            var planeZ2 = new THREE.Plane(new THREE.Vector3(0, 0, -1), 0);
            var planeX = new THREE.Plane(new THREE.Vector3(1, 0, 0), 0);
            var planeX2 = new THREE.Plane(new THREE.Vector3(-1, 0, 0), 0);
            var planeY = new THREE.Plane(new THREE.Vector3(0, 1, 0), 0);
            var planeY2 = new THREE.Plane(new THREE.Vector3(0, -1, 0), 0);
            var mv = new THREE.Vector3(
                (cX / window.innerWidth) * 2 - 1,
                -(cY / window.innerHeight) * 2 + 1,
                0.5 );
            var raycaster = new THREE.Raycaster()
            raycaster.setFromCamera(mv, camera);
            var pos = raycaster.ray.intersectPlane(planeZ);
            if (!pos)
            	var pos = raycaster.ray.intersectPlane(planeZ2);
            if (!pos)
            	var pos = raycaster.ray.intersectPlane(planeX);
            if (!pos)
            	var pos = raycaster.ray.intersectPlane(planeX2);
            if (!pos)
            	var pos = raycaster.ray.intersectPlane(planeY);
            if (!pos)
            	var pos = raycaster.ray.intersectPlane(planeY2);

            return pos;
    }
	
	onRenderFcts.push(function(delta, now) {
		for (var i = 0; i < balles.length; i++) {
			var b = balles[i]
			if (b.expiration < Date.now()) {
				setTimeout(function () {
					balleAnticipeePos.x = 0;
					balleAnticipeePos.y = -10000;
					balleAnticipeePos.z = 0;
				}, refreshTime*2)
				
				b.balle.position.x = 0;
				b.balle.position.y = -10000;
				b.balle.position.z = 0;
				scene.remove( b.balle )
				delete b.balle
				balles.splice(i, 1)
				scene.remove(particleSystem);
			}
			else {
				var vitesseBalle = b.direction.clone()
				vitesseBalle.multiplyScalar( delta*rapiditeBalle )
				setTimeout(function() {
					if(!b || !b.balle) return;
					b.balle.position.add(vitesseBalle)
					scene.add( particleSystem);
					particule(b.balle.position.x, b.balle.position.y, b.balle.position.z)
					
					autresJoueurs.forEach(function(f) {
						var ballPosClone = b.balle.position.clone()
						if (ballPosClone.sub(f.mesh.character.object3d.position).length() < seuilToucher && f.immuneUntil && f.immuneUntil < Date.now()
							&& ratamahatta.character._curAnimation != "crdeath") {
							toucher_joueur(f.id)
							f.mesh.setAnimationName("crpain")
							flipping = true
							b.expiration = Date.now();
						}
					})
					
				}, refreshTime/2)
				balleAnticipeePos.add(vitesseBalle)
			}
		}	
	})
	
	var level = <?php echo $level; ?>+0;
	var xp = <?php echo $xp; ?>+0;
	var kill_laugh_sound = new Audio(root_folder+'audio/kill-laugh.mp3');
	function toucher_joueur(fighterID) {
		$.ajax({
  				type: "POST",
  				url: '<?php echo ROOT_FOLDER; ?>+api/toucher_joueur',
  				data: "tid="+fighterID,
  				dataType: "json"
  		}).done(function(res) {
	  			var oldLevel = parseFloat(level);
	  			var oldXP = parseFloat(xp);
	  			level = res[0];
	  			xp = res[1];
	  			var dead = res[2];
	  			$('#xp-title').text("Niveau "+level+" : "+Math.round(100*xp/level)+"%");
	  			$('#xp').css('width', Math.round(100*xp/level)+"%");
	  			if (dead) {
		  			popBadassCaption(oldLevel < level ? "Level Up!" : "+"+Math.round(100*xp/level - 100*oldXP/oldLevel)+"% d'XP",res[3]);
		  			kill_laugh_sound.currentTime = 0;
		  			kill_laugh_sound.play();
	  			}
	  	});
	}
	function popBadassCaption(a,b) {
		$('#badass-captions span:nth-child(2)').text(a);
		$('#badass-captions span:nth-child(3)').text(b);
		$('#badass-captions span:nth-child(3)').css('color', (b.indexOf("!") != -1 ? "red" : "inherit"))
		$('#badass-captions').addClass("pop");
		setTimeout(function() {$('#badass-captions').removeClass("pop");}, 3500);
	}
	
	
	//////////////////////////////////////////////////////////////////////////////////
	//		CONTROLES CLAVIER & SOURIS	     										//
	//////////////////////////////////////////////////////////////////////////////////
		var audioRun = new Audio(root_folder+'audio/run.mp3');
	audioRun.playbackRate = playerSpeed/2;
	var audioBreathing = new Audio(root_folder+'audio/breathing.mp3');
	var flipping = false
	document.body.addEventListener('keydown', function(event){
		var inputs	= ratamahatta.controls.inputs;
		/* TOUCHES AVANT ARRIERE */
		if( event.keyCode === 90 || event.keyCode === 38)	{
			inputs.up	= true
			if (audioRun.paused && health > 0 ) {
				audioRun.currentTime = 5;
				audioRun.play()
			}
		}
		if( event.keyCode === 83 || event.keyCode === 40 )	{
			inputs.down	= true
			if (audioRun.paused && health > 0) {
				audioRun.currentTime = 5;
				audioRun.play()
			}
		}

		/* TOUCHES LATÉRALES */
		if( event.keyCode === 65)	{
			inputs.moveleft	= true
			if (audioRun.paused && health > 0 ) {
				audioRun.currentTime = 5;
				audioRun.play()
			}
		}
		if( event.keyCode === 69)	{
			inputs.moveright	= true
			if (audioRun.paused && health > 0) {
				audioRun.currentTime = 5;
				audioRun.play()
			}
		}

		/* TOUCHES GAUCHE DROITE */
		if( event.keyCode === 81 || event.keyCode === 37 )	inputs.left	= true
		if( event.keyCode === 68 || event.keyCode === 39)	inputs.right	= true
		
		/* TOUCHE ESPACE */
		if( event.keyCode === 32 )	inputs.space	= true

		/* TOUCHE F */
		if( event.keyCode === 70 )	inputs.f	= true
		
		/* TOUCHE SHIFT */
		if( event.keyCode === 16 )	{inputs.shift	= true;audioRun.playbackRate = playerSpeed/4}
	})




	document.body.addEventListener('keyup', function(event){
		flipping = false
		var inputs	= ratamahatta.controls.inputs;
		/* TOUCHES AVANT ARRIERE */
		if( event.keyCode === 90 || event.keyCode === 38 )	{
			inputs.up	= false
			if (!audioRun.paused){
				audioRun.pause();
				audioRun.currentTime = 0;

			}
		}
		if( event.keyCode === 83 || event.keyCode === 40 )	{
			inputs.down	= false
			if (!audioRun.paused) {
				audioRun.pause();
				audioRun.currentTime = 0;
			}
		}
		/* TOUCHES LATÉRALES */
		if( event.keyCode === 65)	{
			inputs.moveleft	= false
			if (!audioRun.paused) {
				audioRun.pause();
				audioRun.currentTime = 0;
			}
		}
		if( event.keyCode === 69)	{
			inputs.moveright	= false
			if (!audioRun.paused) {
				audioRun.pause();
				audioRun.currentTime = 0;
			}
		}
		/* TOUCHES GAUCHE DROITE */
		if( event.keyCode === 81 || event.keyCode === 37)	inputs.left	= false
		if( event.keyCode === 68 || event.keyCode === 39)	inputs.right	= false
		
		/* TOUCHE ESPACE */
		if( event.keyCode === 32 )	inputs.space	= false

		/* TOUCHE F */
		if( event.keyCode === 70 )	inputs.f	= false
		
		/* TOUCHE SHIFT */
		if( event.keyCode === 16 )	{inputs.shift	= false;audioRun.playbackRate = playerSpeed/2}
	})
	

	document.body.addEventListener('mousedown', function(event){
		var inputs	= ratamahatta.controls.inputs;
		inputs.shoot = true;
	})
	document.body.addEventListener('mouseup', function(event){
		var inputs	= ratamahatta.controls.inputs;
		inputs.shoot = false;
	})


	var mouseY;
	var mouseX;
	document.onmousemove = getMouseXY;
	function getMouseXY(e) {
		mouseY = e.pageY;
		var inputs	= ratamahatta.controls.inputs;
		if (e.pageX - mouseX > 0) {
			inputs.mouseright = true;
			inputs.mouseleft = false;
		} else if (e.pageX - mouseX < 0) {
			inputs.mouseleft = true;
			inputs.mouseright = false;
		} else {
			setTimeout(function() {
				inputs.mouseleft = false;
				inputs.mouseright = false;
			}, 100)
		}
		mouseX = e.pageX
	};
	document.onmouseout = function() {
		mouseX = window.innerWidth/2;
	};

	
	//////////////////////////////////////////////////////////////////////////////////
	//		GESTION DES ANIMATIONS  												//
	//////////////////////////////////////////////////////////////////////////////////
	onRenderFcts.push(function(delta, now){
		var inputs	= ratamahatta.controls.inputs;
		if (health <= 0) {
			ratamahatta.setAnimationName( "crdeath" );
			return;
		}
		if ( inputs.shoot )	 {
			ajouterBalle()
		}
		if (flipping == true) {
			ratamahatta.setAnimationName('salute')
		} else if ( inputs.space )	 {
			ratamahatta.setAnimationName('jump')
		} else if( inputs.up || inputs.down || inputs.moveleft || inputs.moveright){
			if (!inputs.shift) {
				ratamahatta.character.meshBody.duration = 1250*1.85/playerSpeed;
				ratamahatta.character.meshWeapon.duration = 1250*1.85/playerSpeed;
				ratamahatta.setAnimationName('run')
			} else {
				ratamahatta.character.meshBody.duration = 1250*3.5/playerSpeed;
				ratamahatta.character.meshWeapon.duration = 1250*3.5/playerSpeed;
				ratamahatta.setAnimationName('crwalk')
			}
		} else if (inputs.shift) {
			ratamahatta.setAnimationName('crstand')
		} else if (inputs.shoot) {
			ratamahatta.setAnimationName('attack')
		} else if (inputs.f) {
			ratamahatta.setAnimationName('flip')
		}else {
			ratamahatta.setAnimationName('stand')
		}
	})
	
	
	//////////////////////////////////////////////////////////////////////////////////
	//		GRAVITÉ ARTISANALE		  												//
	//////////////////////////////////////////////////////////////////////////////////
	onRenderFcts.push(function(delta, now){
		var attraction = -0.03*cubeSize/100;
		var fighterY = ratamahatta.character.object3d.position.y
		fighterY+=attraction;
		var blocY = getRealY(ratamahatta.character.object3d.position.x , ratamahatta.character.object3d.position.z)
		if (fighterY < blocY) {
			fighterY -= 3.5*attraction*playerSpeed/2
			if (fighterY > blocY)
				fighterY = blocY
		}
		ratamahatta.character.object3d.position.y = fighterY
	})
	
	
	//////////////////////////////////////////////////////////////////////////////////
	//		CAMERA 3e PERSONNE		  												//
	//////////////////////////////////////////////////////////////////////////////////
	onRenderFcts.push(function() {
		var cameraOffset	= new THREE.Vector3(0, 1, -2.5);
		var matrix	= new THREE.Matrix4().makeRotationY(ratamahatta.character.object3d.rotation.y);
		cameraOffset.applyMatrix4( matrix );
		var cpos = ratamahatta.character.object3d.position.clone();
		cpos.add(cameraOffset);
		camera.position.set(cpos.x, cpos.y, cpos.z);
		var lpos = ratamahatta.character.object3d.position.clone();
		var lookOffset = new THREE.Vector3( 0, (window.innerHeight/2 - mouseY)*4.5/window.innerHeight, 0 )
		lpos.add(lookOffset)
		camera.lookAt(lpos);	
	})
	
	
	//////////////////////////////////////////////////////////////////////////////////
	//		RENDU VISUEL															//
	//////////////////////////////////////////////////////////////////////////////////
	onRenderFcts.push(function(){
		renderer.render( scene, camera );		
	})
	
	
	//////////////////////////////////////////////////////////////////////////////////
	//		POST PROCESSING				QUAND ON EST TOUCHER #BALL					//
	//////////////////////////////////////////////////////////////////////////////////
	var postprocessing = {};
	(function () {
		var renderPass = new THREE.RenderPass( scene, camera );

		var bokehPass = new THREE.BokehPass( scene, camera, {
			focus: 		1.1,
			aperture:	0.1,
			maxblur:	1.0,

			width: window.innerWidth,
			height: window.innerHeight
		} );

		bokehPass.renderToScreen = true;

		var composer = new THREE.EffectComposer( renderer );

		composer.addPass( renderPass );
		composer.addPass( bokehPass );

		postprocessing.composer = composer;
		postprocessing.bokeh = bokehPass;

	})()
	
	
	var game_over_sound = new Audio(root_folder+'/audio/game-over.mp3');
	onRenderFcts.push(function(){
		var inputs	= ratamahatta.controls.inputs
		if( inputs.p){
			postprocessing.composer.render( 0.1 );
			if(audioBreathing.currentTime < 5 ) audioBreathing.currentTime = 5;
			if (audioBreathing.paused ) {
				audioBreathing.playbackRate = 3;
				audioBreathing.play()
			}
			if (health <= 0) {
				game_over_sound.currentTime = 0;
				game_over_sound.play();
				ratamahatta.setAnimationName( "crdeath" );
				setTimeout(function() {
					$('#blood-overlay').addClass('visible');
				}, 350);
				setTimeout(function() {
					//$('#container').remove();
					onRenderFcts[0] = function(){};
				}, 550);
			}
		}
	})
	
	
	//////////////////////////////////////////////////////////////////////////////////
	//		BOUCLE DE JEU															//
	//////////////////////////////////////////////////////////////////////////////////
	var lastTimeMsec= null
	requestAnimationFrame(function animate(nowMsec){
		
		// CALCUL DES FPS
		stats.update();	
		
		// MESURE DU TEMPS
		lastTimeMsec	= lastTimeMsec || nowMsec-1000/60
		var deltaMsec	= Math.min(200, nowMsec - lastTimeMsec)
		lastTimeMsec	= nowMsec
		
		// ON EFFECTUE TOUTES NOS FONCTIONS A APPELER A CHAQUE RENDU
		onRenderFcts.forEach(function(onRenderFct){
			onRenderFct(deltaMsec/1000, nowMsec/1000)
		})
		
		// ON REBOUCLE
		requestAnimationFrame( animate );
	})
	
	
	//////////////////////////////////////////////////////////////////////////////////
	//		HELPER FUNCTIONS														//
	//////////////////////////////////////////////////////////////////////////////////
	function onWindowResize() {
		camera.aspect = window.innerWidth / window.innerHeight;
		camera.updateProjectionMatrix();
		renderer.setSize( window.innerWidth, window.innerHeight );
		document.onmouseout()
	}

	function loadTexture( path, callback ) {
		var image = new Image();
		image.onload = function () { callback(); };
		image.src = path;
		return image;
	}
	// Generation des hauteurs de chaque bloc à l'aide d'un bruit de perlin
	function generateHeight( width, height ) {
		var data = [], perlin = new ImprovedNoise(),
		size = width * height, quality = 2, z = 0;
		
		for ( var j = 0; j < 4; j ++ ) {
			if ( j == 0 ) for ( var i = 0; i < size; i ++ ) data[ i ] = 0;
			for ( var i = 0; i < size; i ++ ) {
				var x = i % width, y = ( i / width ) | 0;
				data[ i ] += perlin.noise( x / quality, y / quality, z ) * quality;
			}
			quality *= 4
		}
		return data;
	}

	function getY( x, z ) {
		return ( data[ x + z * worldWidth ] * 0.2 ) | 0;
	}
	
	function getRealY(x,z) {
		x = x/cubeSize/mapScale;
		x+= worldHalfWidth;
		x = Math.round(x);
		z = z/cubeSize/mapScale;
		z+= worldHalfDepth;
		z = Math.round(z);
		y = getY( x, z )
		return y*cubeSize*mapScale + (0.13 + (cubeSize-100)*0.0017);
	}
	
	//////////////////////////////////////////////////////////////////////////////////
	//		RESEAU																	//
	//////////////////////////////////////////////////////////////////////////////////
	var refreshTime = 350;
	setInterval(function() {
		$.ajax({
			dataType : "json",
  			type: "POST",
  			url: '<?php echo ROOT_FOLDER; ?>+api/actualiser_moi',
  			data: "px="+ratamahatta.character.object3d.position.x
  			+"&py="+ratamahatta.character.object3d.position.y
  			+"&pz="+ratamahatta.character.object3d.position.z
  			+"&ry="+ratamahatta.character.object3d.rotation.y
  			+"&animation="+ratamahatta.character._curAnimation
  			+"&bx="+(balles.length > 0 && balles[0].balle ? balles[0].balle.position.x : balleAnticipeePos.x)
  			+"&by="+(balles.length > 0 && balles[0].balle ? balles[0].balle.position.y : balleAnticipeePos.y)
  			+"&bz="+(balles.length > 0 && balles[0].balle ? balles[0].balle.position.z : balleAnticipeePos.z)
  		}).done(function(res) {
	  		if (res[0] < health) {
	  			$('#killer-name').text(res[1]);
	  			health = parseFloat(res[0]);
	  			$('#lifepercentage').text(Math.round(health*100/originalHealth)+" %");
	  			$('#lifepercentage').addClass('life-warning');
	  			setTimeout(function() {
		  			$('#lifepercentage').removeClass('life-warning');
		  		}, 500)
		  		var inputs	= ratamahatta.controls.inputs;
		  		inputs.p = true
		  		setTimeout(function() {
			  		inputs.p = false
			  	}, 3500)
	  		}
  		});
	}, refreshTime);
	
	setInterval(function(){
		$.ajax({
  			type: "POST",
  			url: '<?php echo ROOT_FOLDER; ?>+api/recuperer_liste_joueur',
  			dataType: "json"
  		}).done(function(joueurs) {
  			// Ajout
  			for (var i = 0; i<joueurs.length; i++)
	  			ajouterJoueur(joueurs[i]);
	  		// Suppression
	  		for (var i = 0; autresJoueurs.length > i; i++) {
		  		var present = false
	  			for (var j = 0; j < joueurs.length; j++) {
	  				if (autresJoueurs[i].id == joueurs[j][0])
	  					present = true;
	  			}
	  			if (!present)
	  				retirerJoueur(autresJoueurs[i].id)
	  		}
  		});
	}, refreshTime);
	
	
	setInterval(function() {
		autresJoueurs.forEach(function(j) {
			$.ajax({
  				type: "POST",
  				url: '<?php echo ROOT_FOLDER; ?>+api/recuperer_info_joueur',
  				data: "fighterId="+j.id,
  				dataType: "json"
  			}).done(function(posrot) {
	  			if (j.mesh) {
  					j.posx = posrot[0] 
  					j.posy = posrot[1]
  					j.posz = posrot[2]
  					j.roty = posrot[3]
	  				j.mesh.setAnimationName(posrot[4]) 
  			
  				}
  				if (j.balle) {
  					j.balleposx = posrot[5]
  					j.balleposy = posrot[6]
  					j.balleposz = posrot[7]
  				}
  			})
		});
	}, refreshTime);
	
</script>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-70662996-1', 'auto');
  ga('send', 'pageview');

</script>
</body>
</html>