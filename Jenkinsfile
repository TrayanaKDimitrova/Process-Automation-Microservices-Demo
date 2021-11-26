pipeline {
    agent any
    environment {
        productionVersion = "1.0"
    }
    stages {
        stage('Verify Branch') {
            steps {
                echo "$GIT_BRANCH"
            }
        }
        stage('Docker Build') {
            steps {
                //Configuration production with specific version for images in docker-compose. 
                sh(script: "\$env:VERSION= ${env.productionVersion}; docker-compose build")   
                sh(script: "docker images -a")
            }
        }   
	    stage('Run Test Application') {
            steps {
                //Configuration production with specific version for images in docker-compose. 
                sh(script: "\$env:VERSION= ${env.productionVersion}; docker-compose up -d")    
            }
        }
        stage('Run Integration Tests in Development ') {
            when { branch 'development' }
            steps {
                sh(script: './Tests/DevelopmentTests.ps')      
            }
        }
        stage('Run Integration Tests in Production ') {
            when { branch 'main' }
            steps {
                sh(script: './Tests/ProductionTests.ps')      
            }
        }
        stage('Stop Test Application') {
            steps {
                sh(script: "docker-compose down") 
                sh(script: "docker volume prune -f")   		
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
                        sh(script: "kubectl apply -f ./.k8s/loadbalancers/clients")
                        sh(script: "kubectl apply -f ./.k8s/loadbalancers/services")
                        sh(script: "kubectl apply -f ./.k8s/.environments/development.yml") 
                        sh(script: "kubectl apply -f ./.k8s/web-services/clients")
                        sh(script: "kubectl apply -f ./.k8s/web-services/services") 
                        sh(script: "kubectl apply -f ./.k8s/databases")   
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
                                sh(script: "kubectl apply -f ./.k8s/loadbalancers/clients")
                                sh(script: "kubectl apply -f ./.k8s/loadbalancers/services")
                                sh(script: "kubectl apply -f ./.k8s/.environments/production.yml") 
                                sh(script: "kubectl apply -f ./.k8s/web-services/clients")
                                sh(script: "kubectl apply -f ./.k8s/web-services/services") 
                                sh(script: "kubectl apply -f ./.k8s/databases")   
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
