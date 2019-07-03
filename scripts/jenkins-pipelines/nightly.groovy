//!/usr/bin/env groovy

// Get / Set release name
// TODO - remove hardcoded value
def  apiVersion = '2.5.0' //env.getProperty("apiVersion")
echo apiVersion

pipeline {
    agent any
    stages {
        stage('Cleanup') {
            steps {
                script {
                    // Cleanup previous stuff
                    // Files
                    sh("rm -f .gitignore")

                    // Folders - API job scm files
                    sh("rm -rf .git/ .gitlab/ build/ code/ docs/ scripts/")

                    // Folders - Remaining files
                    sh("rm -rf builds/      builds@tmp/")
                    sh("rm -rf com_api/     com_api@tmp/")
                    sh("rm -rf com_api-scm/ com_api-scm@tmp/")

                    // Make directories needed to generate build
                    // mkdir -p is for creatingparents directories as needed
                    sh("mkdir -p builds")
                    sh("mkdir com_api-scm")
                }
            }
        }

        stage('Checkout') {
            steps {
                script {
                    // This is important, we need clone into different folder here,
                    // Because, as part of tag based pull, we will be cloning same repo again
                    dir('com_api-scm') {
                        checkout scm
                    }
                }
            }
        }

        stage('Init') {
            steps {
                script {
                    // Define subextensions array having unique git repos
                    // @TODO - move this to json itself?
                    def subextensions = ['com_api']

                    // def props = readJSON text: '{}' // Add JSON here or specify file below
                    def props = readJSON file: 'com_api-scm/build/version.json'

                    subextensions.eachWithIndex { item, index ->
                       // Do clone all subextensions repos by checking out corresponding release branch
                       sh("git clone --branch " + props['com_api'][apiVersion][item]['branch'] + " --depth 1 " + props['com_api'][apiVersion][item]['repoUrl'])
                    }
                }
            }
        }

        stage('Copy files') {
            steps {
                script {
                    // Copy com_api from com_api repo into builds folder
                    sh("cp    code/api.xml        builds/")
                    sh("cp    code/script.api.php builds/")

                    sh("cp -r code/admin   builds/")
                    sh("cp -r code/site    builds/")
                    sh("cp -r code/plugins builds/")
                }
            }
        }

        stage('Make zips') {
            steps {
                script {
                    // Get commit id
                    // @TODO - needs to define shortGitCommit at global level
                    def gitCommit      = ''
                    def shortGitCommit = ''

                    // For branch based build - we need the revision number of tag checked out,
                    // So cd into `com_api` dir
                    dir('com_api') {
                        // gitCommit   = env.getProperty('GIT_COMMIT')
                        gitCommit      = sh(returnStdout: true, script: 'git rev-parse HEAD').trim().take(8)
                        shortGitCommit = gitCommit[0..7]
                        echo gitCommit
                        echo shortGitCommit
                    }

                    // Now we are good to create zip for component, exclude unwanted folders
                    // Cleanup build dir folders
                    sh("rm -rf build/com_api")
                    sh("rm -rf build/com_api@tmp")

                    // Now we are good to create zip for package, pass folders to ignore as -x
					// @TODO - for some reasons above rm -rf commands not working,
					// So, need to skip those through -x param of zip command
                    dir('builds/') {
                        sh('zip -rq ../com_api_v' + apiVersion + '_' + shortGitCommit + '.zip . -x "com_api/*" "com_api@tmp/*"')
                    }
                }
            }
        }

        stage('Archive') {
            steps {
                script {
                    // Get commit id
                    // @TODO - needs to define shortGitCommit at global level
                    def gitCommit      = ''
                    def shortGitCommit = ''

                    // For branch based build - we need the revision number of tag checked out,
                    // So cd into `com_api` dir
                    dir('com_api') {
                        // gitCommit   = env.getProperty('GIT_COMMIT')
                        gitCommit      = sh(returnStdout: true, script: 'git rev-parse HEAD').trim().take(8)
                        shortGitCommit = gitCommit[0..7]
                        echo gitCommit
                        echo shortGitCommit
                    }

                    // Archive Artifact
                    archiveArtifacts 'com_api_v' + apiVersion + '_' + shortGitCommit + '.zip'
                }
            }
        }

        stage('Cleanup folders') {
            steps {
                script {
                    // Cleanup previous stuff
                    // Files
                    sh("rm -f .gitignore")

                    // Folders - EB job scm files
                    sh("rm -rf .git/ .gitlab/ build/ com_api/ scripts/ template_overrides/")

                    // Folders - Remaining files
                    sh("rm -rf builds/      builds@tmp/")
                    sh("rm -rf com_api/     com_api@tmp/")
                    sh("rm -rf com_api-scm/ com_api-scm@tmp/")
                    sh("rm -rf scripts/     scripts@tmp/")
                    sh("rm -rf docs/        docs@tmp/")
                }
            }
        }
    }

    post {
      failure {
            mail to: 'manoj_l@techjoomla.com',
                 subject: "Failed Pipeline: ${currentBuild.fullDisplayName}",
                 body: "Something is wrong with ${env.BUILD_URL}"
        }
    }
}
