pragma solidity ^0.5.1;

contract FIX134 {
    bytes public data;
    uint256 public number;
    
    event Say(
        uint256 indexed _number
    );
    
    event Say(
        uint256 indexed _number,
        bytes indexed _data
    );

    function say(uint256 _number) public {
        number = _number;
        emit Say(_number);
    }
    
    function say(uint256 _number, bytes memory _data) public {
        data = _data;
        number = _number;
        emit Say(_number, _data);
    }
}