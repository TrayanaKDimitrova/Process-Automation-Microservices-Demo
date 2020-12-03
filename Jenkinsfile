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
	    stage('Run Test Application') {
            steps {
                powershell(script: 'docker-compose up -d')    
            }
        }
        stage('Run Integration Tests') {
            when { branch 'development' }
                steps {
                    powershell(script: './Tests/DevelopmentTests.ps')      
                }
            when { branch 'main' }
                steps {
                    powershell(script: './Tests/ProductionTests.ps')      
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
                    emailext body: 'Pipeline Finished: Success', recipientProviders: [[$class: 'DevelopersRecipientProvider'], [$class: 'RequesterRecipientProvider']], subject: 'Car Rental System'
                }
                failure {
                    echo "Build failed!"
                    emailext body: 'Pipeline Failed :(', recipientProviders: [[$class: 'DevelopersRecipientProvider'], [$class: 'RequesterRecipientProvider']], subject: 'Car Rental System'
                }
            }
        }
        stage('Push Images') {
	        when { branch 'main' }
                steps {
                    script {
                        docker.withRegistry('https://index.docker.io/v1/', 'MyDockerHubCredentials') {
                            def client = docker.image("3176a6a/demo-carrentalsystem-client-jenkins")
                            client.push(env.BUILD_ID)
                            client.push('latest')
                        }
                    }
                }
                post {
                    success {
                        echo "Images pushed!"
                    }
                    failure {
                        echo "Images push failed!"
                        emailext body: 'Images Push Failed', recipientProviders: [[$class: 'DevelopersRecipientProvider'], [$class: 'RequesterRecipientProvider']], subject: 'Car Rental System'
                    }
                }
        }
        stage('Deploy Development or Production') {
            when { branch 'development' }
                steps {
                    echo "Kuber apply all in Development."
                    // withKubeConfig([credentialsId: 'DevelopmentServer', serverUrl: 'https://35.193.120.112']) {
                    //     powershell(script: 'kubectl apply -f ./.k8s/.environment/development.yml') 
                    //     powershell(script: 'kubectl apply -f ./.k8s/databases')    
                    //     powershell(script: 'kubectl apply -f ./.k8s/web-services') 
                    //     powershell(script: 'kubectl apply -f ./.k8s/clients')
                    }
                }
            when { branch 'main' }
                stages {
                    stage('Input') {
                        steps {
                            input('Do you want to publish production?')
                        }
                    }
                    stage('If publish is clicked') {
                        steps {
                            echo "Kuber apply all in Production."
                            // withKubeConfig([credentialsId: 'ProductionServer', serverUrl: 'https://35.226.255.7']) {
                            //     powershell(script: 'kubectl apply -f ./.k8s/.environment/production.yml') 
                            //     powershell(script: 'kubectl apply -f ./.k8s/databases')
                            //     powershell(script: 'kubectl apply -f ./.k8s/web-services') 
                            //     powershell(script: 'kubectl apply -f ./.k8s/clients')   
                            }
                        }
                        post {
                            success {
                                echo "Images published!"
                            }
                            failure {
                                echo "Images publish failed in ProductionServer!"
                                emailext body: 'Images publish Failed', recipientProviders: [[$class: 'DevelopersRecipientProvider'], [$class: 'RequesterRecipientProvider']], subject: 'Car Rental System'
                            }
                        }
                    }
                }
        }
}