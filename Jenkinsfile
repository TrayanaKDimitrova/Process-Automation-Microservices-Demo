def productionVersion = "1.0"
def developmentVersion = "latest"
pipeline {
    agent any
    stages {
        stage('Verify Branch') {
            steps {
                echo "$GIT_BRANCH"
            }
        }
        stage('Docker Build') {
            steps {
                powershell(script: '$env:VERSION=${env.productionVersion}; docker-compose build')   
                powershell(script: 'docker images -a')
            }
        }   
	    stage('Run Test Application') {
            steps {
                powershell(script: 'docker-compose up -d')    
            }
        }
        stage('Run Integration Tests in Development ') {
            when { branch 'development' }
            steps {
                powershell(script: './Tests/DevelopmentTests.ps')      
            }
        }
        stage('Run Integration Tests in Production ') {
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
                    // Mail notification
                    emailext body: 'Pipeline Finished: Success', recipientProviders: [[$class: 'DevelopersRecipientProvider'], [$class: 'RequesterRecipientProvider']], subject: 'Car Rental System'
                }
                failure {
                    echo "Build failed!"
                    // Mail notification
                    emailext body: 'Pipeline Failed :(', recipientProviders: [[$class: 'DevelopersRecipientProvider'], [$class: 'RequesterRecipientProvider']], subject: 'Car Rental System'
                }
            }
        }
        stage('Push Images') {
	        when { branch 'main' }
                steps {
                    script {
                        docker.withRegistry('https://index.docker.io/v1/', 'MyDockerHubCredentials') {
                            def admin = docker.image("3176a6a/demo-carrentalsystem-admin")
                            admin.push(env.BUILD_ID)
                            admin.push('latest')
                            def client = docker.image("3176a6a/demo-carrentalsystem-client")
                            client.push(env.BUILD_ID)
                            client.push('latest')
                            def server = docker.image("3176a6a/demo-carrentalsystem-server")
                            server.push(env.BUILD_ID)
                            server.push('latest')
                        }
                    }
                }
                post {
                    success {
                        echo "Images pushed!"
                    }
                    failure {
                        echo "Images push failed!"
                        // Mail notification
                        emailext body: 'Images Push Failed', recipientProviders: [[$class: 'DevelopersRecipientProvider'], [$class: 'RequesterRecipientProvider']], subject: 'Car Rental System'
                    }
                }
        }
        stage('Deploy Development') {
            when { branch 'development' }
                steps {
                    //We used trial cloud account have only one publish. This publish is production.
                    //When have dev publish used this configuration in dev bransh. Now dev and local is same.
                    withKubeConfig([credentialsId: 'DevelopmentServer', serverUrl: 'https://localhost']) {
                        powershell(script: 'kubectl apply -f ./.k8s/loadbalancers/clients')
                        powershell(script: 'kubectl apply -f ./.k8s/loadbalancers/services')
                        powershell(script: 'kubectl apply -f ./.k8s/.environments/development.yml') 
                        powershell(script: 'kubectl apply -f ./.k8s/web-services/clients')
                        powershell(script: 'kubectl apply -f ./.k8s/web-services/services') 
                        powershell(script: 'kubectl apply -f ./.k8s/databases')   
                    }
                }
        }
        stage('Deploy Production') {
            when { branch 'main' }
                stages {
                    stage('Input') {
                        steps {
                            input('Do you want to publish production?')
                        }
                    }
                    stage('If publish is clicked') {
                        steps {
                            //
                            withKubeConfig([credentialsId: 'ProductionServer', serverUrl: 'https://35.226.255.7']) {
                                powershell(script: 'kubectl apply -f ./.k8s/loadbalancers/clients')
                                powershell(script: 'kubectl apply -f ./.k8s/loadbalancers/services')
                                powershell(script: 'kubectl apply -f ./.k8s/.environments/production.yml') 
                                powershell(script: 'kubectl apply -f ./.k8s/web-services/clients')
                                powershell(script: 'kubectl apply -f ./.k8s/web-services/services') 
                                powershell(script: 'kubectl apply -f ./.k8s/databases')   
                            }
                        }
                        post {
                            success {
                                echo "Images published!"
                            }
                            failure {
                                echo "Images publish failed in ProductionServer!"
                                // Mail notification
                                emailext body: 'Images publish Failed', recipientProviders: [[$class: 'DevelopersRecipientProvider'], [$class: 'RequesterRecipientProvider']], subject: 'Car Rental System'
                            }
                        }
                    }
            }
        }
    }
}