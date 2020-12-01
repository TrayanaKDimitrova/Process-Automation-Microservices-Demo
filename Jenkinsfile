pipeline {
  agent any
  stages {
    stage('Verify Branch') {
      steps {
        echo "$GIT_BRANCH"
      }
    }
    // stage('Run Unit Tests') {
    //   steps {
    //     powershell(script: """ 
    //       cd Server
    //       dotnet test
    //       cd ..
    //     """)
    //   }
    // }
    stage('Docker Build') {
      steps {
        powershell(script: 'docker-compose build')   
        powershell(script: 'docker images -a')
      }
    }
    // stage ('Build Development/Production Image') {
    //   when { branch 'development' }
	//       steps {
    //         echo "Build Development Image"
	//        //powershell(script: 'docker build -t 3176a6a/demo-carrentalsystem-client-development:latest --build-arg configuration=development ./Client')    
	//       }
    //   when { branch 'main' }
	//     steps {
    //         echo "Build Production Image"
	//        //powershell(script: 'docker build -t 3176a6a/demo-carrentalsystem-client-production:1:0 --build-arg configuration=production ./Client')    
	//     }
    // }
	stage('Run Test Application') {
      steps {
        powershell(script: 'docker-compose up -d')    
      }
    }
    stage('Stop Test Application') {
      steps {
        powershell(script: 'docker-compose down') 
        powershell(script: 'docker volume prune -f')   		
      }
      post {
	    success {
	      echo "Build successfull!"
	    }
	    failure {
	      echo "Build failed!"
	    }
      }
    }
  }
}