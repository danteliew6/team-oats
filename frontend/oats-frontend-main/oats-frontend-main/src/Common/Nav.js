import Navbar from 'react-bootstrap/Navbar'
import Nav from 'react-bootstrap/Nav'
import NavDropdown from 'react-bootstrap/NavDropdown'
import Container from 'react-bootstrap/Container'
import React, { Component } from 'react';
import image from "./carousell-icon.png";
import { red } from '@material-ui/core/colors';
import axios from 'axios';
import Avatar from '@material-ui/core/Avatar';


class NavBar extends Component {

  constructor(props) {
    super(props);
    this.state = {
      user: [],
    };
  }

  handleChatClick(page, e) {
    this.props.setActive(page);
  }

  componentDidMount() {
    var self = this;
    axios.get(process.env.REACT_APP_BE_URL + 'user-details', {
      headers: {
        'Authorization': `Bearer ${this.props.token}` 
      }
    }).then(function(response){
      self.setState({ user: [response.data.data]});
      
    })
  }

  render() {
    const styles = {
      avatar: {
        backgroundColor: red[500],
        width:30,
        height:30,
      },
    }
    
    if (this.state.user[0] === undefined) {
      return <div>Loading...</div>
    }

    return (
      <Navbar collapseOnSelect expand="lg" style = {{backgroundColor:"#2C2C2D"}}>
        <Container>
        <Navbar.Brand>
          <img 
          src = {image}
          width="30"
          height="30"
          />
        </Navbar.Brand>
        <Navbar.Toggle aria-controls="responsive-navbar-nav" />
        <Navbar.Collapse id="responsive-navbar-nav">
          <Nav className="me-auto">
            <Nav.Link onClick={this.handleChatClick.bind(this,'product')} style={{color:'white'}}>Listing</Nav.Link>
            <Nav.Link onClick={this.handleChatClick.bind(this,'conversation')} style={{color:'white'}}>Chat</Nav.Link>
          </Nav>
          <Nav className="ml-auto">
            <Avatar aria-label="User" style={styles.avatar}>
                          {this.state.user[0].username.substring(0, 1)}
            </Avatar>
            <Nav.Link style={{color:'white'}}>Hello, {this.state.user[0].username}</Nav.Link>
            <Nav.Link style={{color:'white'}}>Caroupoints: {this.props.caroupoints}</Nav.Link>
            <Nav.Link style={{color:'white'}} href="/">Logout</Nav.Link>
          </Nav>
        </Navbar.Collapse>
        </Container>
      </Navbar>
    );
  }
}

export default NavBar;