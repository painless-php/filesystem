## TODO

* Test Directory::move()

* Configuration
    * option to map top level returned values
    ' ConfigurationFactory
        * $config = ConfigurationFactory::returnFilenames()->create();
    * Use ::CHILD_FIRST?


* Use iterator for Directory recursive deleteContents
* Use iterator for Directory recursive copy

* FilesystemObject::getRelativePath() should work with both children and parent paths
* FilesystemObject::getAbsolutePath() needs a rewrite

* FileLineIterator
    * Support filters with StringFilterInterface
    * writeContent() is really inefficient. Support in-memory modification?
        * WriterInterface


* ComposerJson::locate() (using Filesystem::findUpwards)
* Env editable class

* Make sure that proper errors are thrown when trying to operate on a non-existent file
* Make sure that permission errors throw correct exceptions
* use file's real path for exceptions?

* Directory::delete allow exclusion by relative path and absolute path
