pragma solidity ^0.5.1;

contract FIX125 {
    
    bytes public data;
    
    event SetData(
        bytes indexed _data
    );
    
    function setData(bytes memory _data) public {
        data = _data;
        emit SetData(_data);
    }
}