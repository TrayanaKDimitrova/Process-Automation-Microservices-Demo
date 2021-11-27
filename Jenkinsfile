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
                sh(script: 'docker-compose build')   
                sh(script: 'docker images -a')
            }
        }   
	    stage('Run Test Application') {
            steps {
                sh(script: 'docker-compose up -d')    
            }
        }
        stage('Run Integration Tests in Development ') {
            when { branch 'development' }
            steps {
                pwsh(script: '/var/jenkins_home/workspace/New_Test_Pipeline_development/Tests/DevelopmentTests.ps')      
            }
        }
        stage('Run Integration Tests in Production ') {
            when { branch 'main' }
            steps {
                pwsh(script: './Tests/ProductionTests.ps')      
            }
        }
        stage('Stop Test Application') {
            steps {
                pwsh(script: 'docker-compose down') 
                pwsh(script: 'docker volume prune -f')   		
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
                    // We have only one publish in cloud becouse have tial account. Code is work and testing with production IP. 
                    //For this test reason we use Development and local is same publish for this project
                    echo "Apply kubernetes apply files in development."
                    withKubeConfig([credentialsId: 'DevelopmentServer', serverUrl: 'https://127.0.0.1']) {
                        pwsh(script: 'kubectl apply -f ./.k8s/loadbalancers/clients')
                        pwsh(script: 'kubectl apply -f ./.k8s/loadbalancers/services')
                        pwsh(script: 'kubectl apply -f ./.k8s/.environments/development.yml') 
                        pwsh(script: 'kubectl apply -f ./.k8s/web-services/clients')
                        pwsh(script: 'kubectl apply -f ./.k8s/web-services/services') 
                        pwsh(script: 'kubectl apply -f ./.k8s/databases')
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
                            //In this branch we don't have production.yml.
                            //And for security there missing IP for production.
                            withKubeConfig([credentialsId: 'ProductionServer']) {
                                pwsh(script: 'kubectl apply -f ./.k8s/loadbalancers/clients')
                                pwsh(script: 'kubectl apply -f ./.k8s/loadbalancers/services')
                                pwsh(script: 'kubectl apply -f ./.k8s/.environments/production.yml') 
                                pwsh(script: 'kubectl apply -f ./.k8s/web-services/clients')
                                pwsh(script: 'kubectl apply -f ./.k8s/web-services/services') 
                                pwsh(script: 'kubectl apply -f ./.k8s/databases')   
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
