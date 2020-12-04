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
                powershell(script: 'docker-compose build')   
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
                            // def client = docker.image("3176a6a/demo-carrentalsystem-client-jenkins")
                            // client.push(env.BUILD_ID)
                            // client.push('latest')
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
                        emailext body: 'Images Push Failed', recipientProviders: [[$class: 'DevelopersRecipientProvider'], [$class: 'RequesterRecipientProvider']], subject: 'Car Rental System'
                    }
                }
        }
        stage('Deploy Development') {
            when { branch 'development' }
                steps {
                    echo "Kubernetes apply files."
                    //withKubeConfig([credentialsId: 'DevelopmentServer', serverUrl: 'https://localhost']) {
                        powershell(script: 'kubectl apply -f .k8s/loadbalancers/clients')
                        //powershell(script: 'kubectl apply -f ./.k8s/loadbalancers/services')
                        //powershell(script: 'kubectl apply -f ./.k8s/.environment/development.yml') 
                        //powershell(script: 'kubectl apply -f ./.k8s/web-services/clients')
                        //powershell(script: 'kubectl apply -f ./.k8s/web-services/services') 
                        //powershell(script: 'kubectl apply -f ./.k8s/databases')   
                   // }
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
                            withKubeConfig([credentialsId: 'ProductionServer']) {
                                //powershell(script: 'kubectl apply -f ./.k8s/loadbalancers/clients')
                                //powershell(script: 'kubectl apply -f ./.k8s/loadbalancers/services')
                                powershell(script: 'kubectl apply -f ./.k8s/.environment/production.yml') 
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
                                emailext body: 'Images publish Failed', recipientProviders: [[$class: 'DevelopersRecipientProvider'], [$class: 'RequesterRecipientProvider']], subject: 'Car Rental System'
                            }
                        }
                    }
            }
        }
    }
}